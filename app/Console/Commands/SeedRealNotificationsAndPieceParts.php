<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedRealNotificationsAndPieceParts extends Command
{
    protected $hidden = true;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:seed_real_notifications_and_piece_parts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves text files from Azure and seed database with data.';

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
        Artisan::call('db:seed', ['--class' => 'RealNotificationsTableSeeder']);
        Artisan::call('db:seed', ['--class' => 'NotificationPiecePartTableSeeder']);
        
        $this->line('Notifications and Piece Parts seeder finished. Check email for any errors.');
    }
}
