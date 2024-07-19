<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeleteCipherFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'files:delete-cipher';
    protected $signature = 'app:delete-cipher-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete cipher files older than one hour';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = Storage::allFiles('cipher-files'); // Adjust the path as needed
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

