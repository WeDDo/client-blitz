<?php

namespace App\Jobs;

use App\Services\FileRipper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FileRipJob implements ShouldQueue
{
    use Queueable;

    public array $boards = [];
    public array $keywords = [];

    /**
     * Create a new job instance.
     */
    public function __construct(array $boards, array $keywords)
    {
        $this->boards = $boards;
        $this->keywords = $keywords;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new FileRipper())->rip($this->boards, $this->keywords);
    }
}
