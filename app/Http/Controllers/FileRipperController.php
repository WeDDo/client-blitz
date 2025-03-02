<?php

namespace App\Http\Controllers;

use App\Http\Requests\RipRequest;
use App\Jobs\FileRipJob;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FileRipperController extends Controller
{
    private array $allBoards;
    private string $translate = 'modules/fileripper';

    public function __construct()
    {
        $this->allBoards = collect(__("$this->translate.all_boards"))
            ->map(function ($name, $boardCode) {
                return ['code' => $boardCode, 'name' => $name];
            })
            ->values()
            ->toArray();
    }

    public function index(): Response
    {
        $boards = collect(config('app.fileripper.boards', []))->map(function ($board) {
            return collect($this->allBoards)->where('code', $board)->first();
        })->values()->toArray();

        return Inertia::render('Modules/FileRipper', [
            'boards' => $boards,
            'keywords' => implode(',', config('app.fileripper.keywords')),

            'selects' => [
                'boards' => $this->allBoards,
            ],
        ]);
    }

    public function rip(RipRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $keywords = explode(',', $data['keywords']);
        FileRipJob::dispatch($data['boards'], $keywords)->onQueue('rip');

        return back()->with('success', __("$this->translate.rip_success_message"));
    }
}
