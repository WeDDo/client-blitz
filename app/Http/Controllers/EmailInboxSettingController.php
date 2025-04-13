<?php

namespace App\Http\Controllers;

use App\DataTables\EmailInboxSettings\EmailInboxSettingDataTable;
use App\DataTables\EmailSettings\EmailSettingDataTable;
use App\Http\Requests\EmailInboxSettingRequest;
use App\Models\EmailInboxSetting;
use App\Services\EmailInboxSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class EmailInboxSettingController extends Controller
{
    public function __construct(private EmailInboxSettingService $emailInboxSettingService)
    {
    }

    public function index(): Response
    {
        return inertia('modules/email-inbox-settings/index', [
            'main' => [
//                'translate' => $this->mainTranslate,
            ],
            'data_table' => (new EmailInboxSettingDataTable())->getData(),
        ]);
    }

    public function create(): Response
    {
        return inertia('modules/email-inbox-settings/create', [
            'options' => $this->getOptions(),
        ]);
    }

    public function store(EmailInboxSettingRequest $request): RedirectResponse
    {
        $this->emailInboxSettingService->store($request->validated());
        return redirect()->route('modules.email-inbox-settings.index');
    }

    public function show(EmailInboxSetting $emailInboxSetting): Response
    {
        return inertia('modules/email-inbox-settings/edit', [
            'item' => $emailInboxSetting,
            'options' => $this->getOptions(),
        ]);
    }

    public function update(EmailInboxSettingRequest $request, EmailInboxSetting $emailInboxSetting): RedirectResponse
    {
        $this->emailInboxSettingService->update($request->validated(), $emailInboxSetting);

        return redirect()->route('modules.email-inbox-settings.show', $emailInboxSetting->id);

    }

//    public function destroy(EmailInboxSetting $emailInboxSetting): JsonResponse
//    {
//        $this->emailInboxSettingService->destroy($emailInboxSetting);
//        return response()->json([], 204);
//    }

    private function getOptions(): array
    {
        return [
            'email_settings' => auth()->user()->emailSettings,
        ];
    }

    public function importIndex(): Response|RedirectResponse
    {
        try {
            return inertia('modules/email-inbox-settings/import/index', [
                'data' => $this->emailInboxSettingService->importIndex(),
                'options' => auth()->user()->imapEmailSettings,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function importStore(): RedirectResponse
    {
        $this->emailInboxSettingService->importStore(request()->all());

        return redirect()->route('modules.email-inbox-settings.index');
    }
}
