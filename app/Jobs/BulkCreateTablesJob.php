<?php

namespace App\Jobs;

use App\Models\BusinessLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
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
    public $timeout = 60; // 1 minute to dispatch jobs

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
    public function handle()
    {
        try {
            Log::info("Starting bulk table creation batch for business {$this->businessId}");

            $business = BusinessLink::find($this->businessId);

            if (!$business) {
                Log::error("Business {$this->businessId} not found");
                return;
            }

            // Create an array of jobs for batching
            $jobs = [];

            for ($i = 1; $i <= $this->count; $i++) {
                $tableNumber = $this->prefix . ' ' . $i;

                $jobs[] = new CreateSingleTableJob(
                    $this->businessId,
                    $tableNumber,
                    $this->seats,
                    $business->subdomain
                );
            }

            // Dispatch jobs as a batch
            $batch = Bus::batch($jobs)
                ->name("Bulk Create {$this->count} Tables for Business {$this->businessId}")
                ->allowFailures() // Continue even if some jobs fail
                ->onQueue('tables') // Use a dedicated queue for table operations
                ->dispatch();

            Log::info("Batch {$batch->id} created with {$this->count} table creation jobs");

        } catch (\Exception $e) {
            Log::error('Error creating bulk table batch: ' . $e->getMessage());
            throw $e;
        }
    }
}
