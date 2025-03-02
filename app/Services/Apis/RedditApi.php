<?php

namespace App\Services\Auth\Apis;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RedditApi
{
//    public function getPosts(): array
//    {
//        // Step 1: Obtain the access token
//        $response = Http::withHeaders([
//            'Authorization' => 'Basic ' . base64_encode('YzISQlBBDeLtWym0Tr93Nw:nP7dItLXGDwo0TR3Td5ncbZVnBrOOQ'),
//            'Content-Type' => 'application/x-www-form-urlencoded',
//            'User-Agent' => 'ripper/1.0'
//        ])->asForm()->post('https://www.reddit.com/api/v1/access_token', [
//            'grant_type' => 'client_credentials',
//        ]);
//
//        // Check if the token request was successful
//        if ($response->successful()) {
//            $token = $response->json()['access_token'];
//
//            // Step 2: Fetch posts from the subreddit
//            $postsResponse = Http::withHeaders([
//                'Authorization' => 'Bearer ' . $token,
//                'User-Agent' => 'ripper/1.0'
//            ])->get('https://oauth.reddit.com/r/pornrelapsed/hot');
//
//            // Check if the posts request was successful
//            if ($postsResponse->successful()) {
//                $postsData = $postsResponse->json();
//
//                // Step 3: Initialize an array to hold extracted post details
//                $posts = [];
//
//                // Step 4: Iterate over each post in the 'children' array
//                foreach ($postsData['data']['children'] as $post) {
//                    // Extract desired information from each post
//                    $posts[] = $post['data'];
//                }
//
//                // Return the array of posts
//                return $posts;
//            } else {
//                // Handle errors in fetching posts
//                // Log the error or throw an exception
//                return ['error' => 'Failed to fetch posts: ' . $postsResponse->body()];
//            }
//        } else {
//            // Handle errors in obtaining the access token
//            // Log the error or throw an exception
//            return ['error' => 'Failed to obtain access token: ' . $response->body()];
//        }
//    }
//
//    public function downloadMediaFromPosts(): void
//    {
//        $posts = $this->getPosts();
//
//        foreach ($posts as $post) {
//            $postId = $post['id'] ?? uniqid(); // Use Reddit post ID, fallback to unique ID
//
//            if (isset($post['url_overridden_by_dest'])) {
//                $mediaUrl = $post['url_overridden_by_dest'];
//                $this->downloadFile($mediaUrl, $postId);
//            } elseif (isset($post['media']['reddit_video']['fallback_url'])) {
//                $mediaUrl = $post['media']['reddit_video']['fallback_url'];
//                $this->downloadFile($mediaUrl, $postId);
//            } elseif (isset($post['media']['oembed']['thumbnail_url'])) {
//                $mediaUrl = $post['media']['oembed']['thumbnail_url'];
//                $this->downloadFile($mediaUrl, $postId);
//            }
//        }
//    }
//
//    private function downloadFile(string $url, string $postId): void
//    {
//        $response = Http::withOptions(['stream' => true])->get($url);
//
//        if ($response->successful()) {
//            $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
//            $extension = $pathInfo['extension'] ?? $this->guessExtension($response->header('Content-Type'));
//
//            // Validate extension and default to jpg if unknown
//            if (!$extension) {
//                $extension = 'jpg';
//            }
//
//            // Define filename
//            $filename = "{$postId}.{$extension}";
//            $filePath = storage_path("app/public/reddit/{$filename}");
//
//            // Stream and store the file properly
//            $fileHandle = fopen($filePath, 'w');
//            foreach ($response->toPsrResponse()->getBody() as $chunk) {
//                fwrite($fileHandle, $chunk);
//            }
//            fclose($fileHandle);
//        }
//    }
//
//    private function guessExtension(?string $mimeType): ?string
//    {
//        $mimeMap = [
//            'image/jpeg' => 'jpg',
//            'image/png' => 'png',
//            'image/gif' => 'gif',
//            'video/mp4' => 'mp4',
//            'video/webm' => 'webm',
//        ];
//
//        return $mimeMap[$mimeType] ?? null;
//    }

}
