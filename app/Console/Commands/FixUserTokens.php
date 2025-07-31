<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixUserTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-user-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique user_token values for customer_details table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = 0;

        DB::table('customer_details')
            ->whereNull('user_token')
            ->orWhere('user_token', '')
            ->get()
            ->each(function ($user) use (&$updated) {
                DB::table('customer_details')
                    ->where('id', $user->id)
                    ->update(['user_token' => (string) Str::uuid()]);
                $updated++;
            });

        $this->info("✅ Updated {$updated} user_token values successfully.");
    }
}
