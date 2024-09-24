<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanUpBlobArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:clean_up_blob_archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Delete files older than a given number of days.";
    
    protected $filesToDelete = [];
    
    protected $retentionDays;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->retentionDays = env('RETENTION_DAYS', 90);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        
        $this->info('Getting file list...');
        
        $files = Storage::disk('azure-blob-storage-archive')->allFiles();
        
        $collection = collect($files);
        
        if (count($collection)) {
            
            $this->info('Getting files to delete list...');
            
            $bar = $this->output->createProgressBar(count($collection));
            
            foreach ($collection->chunk(100) as $chunk) {
                foreach ($chunk as $file) {
                    $diffInDays = Carbon::createFromTimestamp(Storage::disk('azure-blob-storage-archive')->lastModified($file))->diffInDays($now);
                    
                    if ($diffInDays > $this->retentionDays) {
                        $this->filesToDelete[] = $file;
                        
                        $bar->advance();
                    }
                }
            }
        }
        
        $bar->finish();
        
        $this->info('Deleting files...');
        
        $collection = collect($this->filesToDelete);
        
        if (count($collection)) {
            
            $bar = $this->output->createProgressBar(count($collection) / 100);
            
            foreach ($collection->chunk(100) as $chunk) {
                Storage::disk('azure-blob-storage-archive')->delete($chunk->toArray());
                
                $bar->advance();
            }
        }
        
        $bar->finish();
        
        $this->info('Blob archive clean up complete. ' . count($collection) . 'files deleted.');
    }
}
