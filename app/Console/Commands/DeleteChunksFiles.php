<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeleteChunksFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-chunks-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Chunks files older than one hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = Storage::allFiles('chunks'); // Adjust the path as needed
        $now = Carbon::now();

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(Storage::lastModified($file));
            if ($lastModified->diffInHours($now) >= 1) {
                Storage::delete($file);
                $this->info("Deleted: $file");
            }
        }

        return 0;
    }
}
