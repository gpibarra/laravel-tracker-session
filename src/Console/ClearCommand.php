<?php

namespace gpibarra\TrackerSession\Console;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use gpibarra\TrackerSession\TrackerSessionServiceProvider;

class ClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracker-session:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old records from the session track';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Clearing session track...');

        $days = config('tracker-session.older');
        $from = Carbon::now()->subDays($days)->format('Y-m-d H:i:s');

        $trackerSessionClass = TrackerSessionServiceProvider::determineSessionModel();
        $trackerSessionClass::where('login_at', '<', $from)->delete();

        $this->info('Session track cleared successfully.');
    }
}
