<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\AutoLoginToken;
use App\ResetPasswordToken;

class DeleteTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete_tokens:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete tokens where expires exceeded';

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
        AutoLoginToken::delete_expired_tokens();
        ResetPasswordToken::delete_expired_tokens();
    }
}
