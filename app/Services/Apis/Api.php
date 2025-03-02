<?php

namespace App\Services\Auth\Apis;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Api
{
    private string $url = 'https://a.4cdn.org';
    private string $fileUrl = 'https://i.4cdn.org';

    public function getBoards(): array
    {
        return Http::get("$this->url/boards.json")->json();
    }

    public function getCatalog(string $board): array
    {
        return Http::get("$this->url/$board/catalog.json")->json();
    }

    public function getThreads(string $board): array
    {
        return Http::get("$this->url/$board/threads.json")->json();
    }

    public function getThread(string $board, string $threadId): array
    {
        return Http::get("$this->url/$board/thread/$threadId.json")->json();
    }

    public function getFile(string $board, string $imageId): Response
    {
        return Http::get("$this->fileUrl/$board/$imageId");
    }
}
