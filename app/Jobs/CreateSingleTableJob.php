<?php

namespace App\Jobs;

use App\Models\BusinessLink;
use App\Models\TableLinkQrData;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateSingleTableJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $businessId;
    public $tableNumber;
    public $seats;
    public $subdomain;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120; // 2 minutes per table

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($businessId, $tableNumber, $seats, $subdomain)
    {
        $this->businessId = $businessId;
        $this->tableNumber = $tableNumber;
        $this->seats = $seats;
        $this->subdomain = $subdomain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(QrCodeService $qrCodeService)
    {
        // Skip if batch is cancelled
        if ($this->batch()->cancelled()) {
            return;
        }

        try {
            Log::info("Creating table {$this->tableNumber} for business {$this->businessId}");

            $business = BusinessLink::find($this->businessId);

            if (!$business) {
                Log::error("Business {$this->businessId} not found");
                return;
            }

            // Skip if exists
            $exists = TableLinkQrData::where('business_link_id', $this->businessId)
                ->where('table_number', $this->tableNumber)
                ->exists();

            if ($exists) {
                Log::info("Table {$this->tableNumber} already exists, skipping");
                return;
            }

            // Generate QR code (this will upload to Cloudinary)
            $qrCodeUrl = $this->subdomain . '.localhost:3000?table=' . $this->tableNumber;
            $qrCode = $qrCodeService->generateTableQR($this->subdomain, $this->tableNumber);

            // Create table record
            TableLinkQrData::create([
                'business_link_id' => $this->businessId,
                'table_number' => $this->tableNumber,
                'seats' => $this->seats,
                'qr_code_url' => $qrCodeUrl,
                'table_qr_code' => $qrCode,
                'status' => 'active'
            ]);

            Log::info("Successfully created table {$this->tableNumber}");

        } catch (\Exception $e) {
            Log::error("Error creating table {$this->tableNumber}: " . $e->getMessage());

            // Rethrow to allow job to retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Failed to create table {$this->tableNumber} after {$this->tries} attempts: " . $exception->getMessage());
    }
}
