<?php

namespace App\Console\Commands;

use Log;
use Illuminate\Console\Command;
use App\Models\SlackOrg;
use App\Models\SlackChannel;

class SyncOrgs extends Command
{
    protected $SLEEP_TIME = 42;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:sync:orgs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync slack organizations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // quick and dirty
        while (42) {
            // quick and dirty
            try {
                $org = $this->fetchSlackOrgToSync();
                if ($org != NULL) {
                    Log::info("Sync'ing org $org->domain");
                    $org->sync_channels();
                }
            } catch (\Exception $e) {
                // catch all motherfucker
                Log::error($e);
            }

            // sleep for preserve rate limits
            // quick and dirty
            sleep($this->SLEEP_TIME);
        }
    }

    private function fetchSlackOrgToSync()
    {
        return SlackOrg::whereNotNull("synced_at")
            ->orderBy("synced_at", "asc")
            ->first();
    }
}
