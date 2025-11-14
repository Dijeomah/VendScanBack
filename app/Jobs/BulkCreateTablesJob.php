<?php

namespace App\Jobs;

use App\Models\BusinessLink;
use App\Models\TableLinkQrData;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BulkCreateTablesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $businessId;
    public $count;
    public $prefix;
    public $seats;
    public $vendorId;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 10 minutes

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($businessId, $count, $prefix, $seats, $vendorId)
    {
        $this->businessId = $businessId;
        $this->count = $count;
        $this->prefix = $prefix;
        $this->seats = $seats;
        $this->vendorId = $vendorId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(QrCodeService $qrCodeService)
    {
        try {
            Log::info("Starting bulk table creation for business {$this->businessId}");

            $business = BusinessLink::find($this->businessId);

            if (!$business) {
                Log::error("Business {$this->businessId} not found");
                return;
            }

            $createdCount = 0;

            for ($i = 1; $i <= $this->count; $i++) {
                $tableNumber = $this->prefix . ' ' . $i;

                // Skip if exists
                $exists = TableLinkQrData::where('business_link_id', $this->businessId)
                    ->where('table_number', $tableNumber)
                    ->exists();

                if ($exists) {
                    Log::info("Table {$tableNumber} already exists, skipping");
                    continue;
                }

                try {
                    $qrCodeUrl = $business->subdomain . '.localhost:3000?table=' . $tableNumber;
                    $qrCode = $qrCodeService->generateTableQR($business->subdomain, $tableNumber);

                    TableLinkQrData::create([
                        'business_link_id' => $this->businessId,
                        'table_number' => $tableNumber,
                        'seats' => $this->seats,
                        'qr_code_url' => $qrCodeUrl,
                        'table_qr_code' => $qrCode,
                        'status' => 'active'
                    ]);

                    $createdCount++;
                    Log::info("Created table {$tableNumber}");

                } catch (\Exception $e) {
                    Log::error("Error creating table {$tableNumber}: " . $e->getMessage());
                    // Continue with next table even if one fails
                }
            }

            Log::info("Bulk table creation completed. Created {$createdCount} out of {$this->count} tables");

        } catch (\Exception $e) {
            Log::error('Error in bulk table creation job: ' . $e->getMessage());
            throw $e;
        }
    }
}
