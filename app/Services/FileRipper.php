<?php

namespace App\Services;

use App\Events\GlobalNewFileEvent;
use App\Events\NewFileEvent;
use App\Models\DownloadedFile;
use App\Services\Auth\Apis\Api;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use InvalidArgumentException;

class FileRipper
{
    private string $date;

    private array $boards;
    private Api $api;
    private array $downloadedFilesData = [];
    private array $downloadedFilesDataBaseData;

    private bool $folderByKeyword = true;
    private array $keywords;
    private array $threadKeywordMap = [];

    public function __construct()
    {
        $this->boards = config('app.fileripper.boards');
        $this->keywords = config('app.fileripper.keywords');
        $this->api = new Api();
        $this->downloadedFilesDataBaseData = DownloadedFile::query()->get()->toArray();
    }

    public function rip(?array $boards = null, ?array $keywords = null): void
    {
        if ($boards && $keywords && count($boards) && count($keywords)) {
            $this->boards = $boards;
            $this->keywords = $keywords;
        }

        $this->date = now()->format('Y-m-d');

        foreach ($this->boards as $board) {
            $catalog = $this->api->getCatalog($board);
            Sleep::sleep(1);

            $keywordThreadIds = $this->getKeywordThreadIds($catalog);

            $allBoardThreads = $this->api->getThreads($board);
            Sleep::sleep(1);

            foreach ($allBoardThreads as $threadPage) {
                foreach ($threadPage['threads'] as $threadPageThread) {
                    $threadId = $threadPageThread['no'];

                    if (!in_array($threadId, $keywordThreadIds)) {
                        continue;
                    }

                    $thread = $this->api->getThread($board, $threadId);
                    Sleep::sleep(1);

                    $this->processThreadPosts($board, $thread['posts'], $threadId);
                }
            }

            if (!empty($this->downloadedFilesData)) {
                DownloadedFile::query()->insert($this->downloadedFilesData);
            }
        }

        echo "Done. \n";
    }

    /**
     * @param string $directory
     * @param string|null $mode modes: [null, 'resolution']
     * @return void
     */
    public function shuffleDirectory(string $directory, ?string $mode = null): void
    {
        if (!File::isDirectory($directory)) {
            throw new InvalidArgumentException("Invalid directory: $directory");
        }

        $files = File::files($directory);
        $existingNames = collect($files)->map(fn($file) => pathinfo($file, PATHINFO_FILENAME))->toArray();
        $filePrefix = '';

        foreach ($files as $file) {
            $extension = $file->getExtension();

            if ($mode === 'resolution') {
                $imageSize = @getimagesize($file->getPathname());

                if ($imageSize) {
                    [$width, $height] = $imageSize;
                    $filePrefix = "{$width}x{$height}_";
                }
            }

            do {
                $newName = $filePrefix . rand(100000, 999999);
            } while (in_array($newName, $existingNames));

            $existingNames[] = $newName;

            $newPath = $file->getPath() . DIRECTORY_SEPARATOR . "$newName.$extension";
            File::move($file->getPathname(), $newPath);
        }
    }

    private function searchForKeywords(array $data): ?string
    {
        if (count($this->keywords) === 0) {
            return null;
        }

        $comment = $data['com'] ?? '';
        foreach ($this->keywords as $keyword) {
            if (str_contains(strtolower($comment), strtolower($keyword))) {
                return $keyword;
            }
        }

        return null;
    }

    private function getKeywordThreadIds(array $catalog): array
    {
        $keywordThreadIds = [];
        foreach ($catalog as $catalogPage) {
            foreach ($catalogPage['threads'] as $catalogThread) {
                $firstKeyword = $this->searchForKeywords($catalogThread);
                if ($firstKeyword) {
                    $keywordThreadIds[] = $catalogThread['no'];
                    $this->threadKeywordMap[$catalogThread['no']] = $firstKeyword;
                }
            }
        }

        return $keywordThreadIds;
    }

    private function processThreadPosts(string $board, array $posts, int $threadId): void
    {
        foreach ($posts as $post) {
            $this->savePostFile($board, $post, $threadId);
        }
    }

    private function savePostFile(string $board, array $post, int $threadId): void
    {
        $filename = $post['filename'] ?? null;
        $tim = $post['tim'] ?? null;
        $ext = $post['ext'] ?? null;

        $allDownloadedFilesData = collect(array_merge($this->downloadedFilesDataBaseData, $this->downloadedFilesData));
        if ($allDownloadedFilesData->contains(fn($file) => $file['board'] === $board && strval($file['post_id']) === strval($post['no']))) {
            return;
        }

        if (!$filename || !$ext || !$tim) {
            return;
        }

        $fullFilename = "{$tim}{$ext}";

        try {
            $file = $this->api->getFile($board, $fullFilename);
            Sleep::sleep(1);

            $this->downloadedFilesData[] = [
                'board' => $board,
                'post_id' => $post['no'],
                'file_id' => $post['tim'],
            ];

            $keyWord = $this->folderByKeyword && ($this->threadKeywordMap[$threadId] ?? null) ? "{$this->threadKeywordMap[$threadId]}/" : '';

            $path = "$this->date/$board/$keyWord";
            $filePath = "$path/$fullFilename";
            Storage::disk('public')->put($filePath, $file->body());

            $fileUrl = Storage::url($filePath);
            NewFileEvent::dispatch($board, $fileUrl, $fullFilename, rtrim($path, '/'));
            GlobalNewFileEvent::dispatch($fileUrl, $fullFilename, rtrim($path, '/'));

            echo "File downloaded. $path \n";
        } catch (\Throwable $exception) {
            echo "File download failed. \n";
        }
    }
}
