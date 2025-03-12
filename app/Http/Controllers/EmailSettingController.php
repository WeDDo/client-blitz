<?php

namespace App\Http\Controllers;

use App\DataTables\EmailSettings\EmailSettingDataTable;
use App\Http\Requests\EmailSettingRequest;
use App\Models\EmailSetting;
use App\Services\EmailSettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class EmailSettingController extends Controller
{
    public function __construct(private EmailSettingService $emailSettingService)
    {
    }

    public function index(): Response
    {
        return inertia('modules/email-settings/index', [
            'main' => [
//                'translate' => $this->mainTranslate,
            ],
            'data_table' => (new EmailSettingDataTable())->getData(),
        ]);
    }

    public function create(): Response
    {
        return inertia('modules/email-settings/create', [
            'options' => $this->getOptions(),
        ]);
    }

    public function store(EmailSettingRequest $request): RedirectResponse
    {
        $this->emailSettingService->store($request->validated());

        return redirect()->route('modules.email-settings.index');
    }

    public function show(EmailSetting $emailSetting): Response
    {
        return inertia('modules/email-settings/edit', [
            'item' => $this->emailSettingService->show($emailSetting),
            'options' => $this->getOptions(),
        ]);
    }

    public function update(EmailSettingRequest $request, EmailSetting $emailSetting): RedirectResponse
    {
        $this->emailSettingService->update($request->validated(), $emailSetting);

        return redirect()->route('modules.email-settings.show', $emailSetting->id);
    }

//    public function destroy(EmailSetting $emailSetting): RedirectResponse
//    {
//        $this->emailSettingService->delete($emailSetting);
//
//        return redirect()->route('email-settings.index');
//    }

    private function getOptions(): array
    {
        return [
            'types' => collect(EmailSetting::getTypes())->map(fn($type) => ['code' => $type, 'name' => $type]),
            'protocols' => collect(EmailSetting::getProtocols())->map(fn($protocol) => ['code' => $protocol, 'name' => $protocol]),
        ];
    }

    public function checkConnection(EmailSetting $emailSetting): RedirectResponse
    {
        try {
            $this->emailSettingService->checkConnection($emailSetting);
            return back()->with('success', 'Connection successful');
        } catch (\Throwable $e) {
            return back()->with('error', 'Connection failed: ' . $e->getMessage());
        }
    }
//    public function show(EmailSetting $emailSetting): JsonResponse
//    {
//        $emailSetting = $this->emailSettingService->show($emailSetting);
//
//        return response()->json([
//            'item' => $emailSetting,
//            'additional' => $emailSetting->getAdditionalData(),
//        ]);
//    }
//
//
//    public function update(EmailSettingRequest $request, EmailSetting $emailSetting): JsonResponse
//    {
//        $this->emailSettingService->update($request->validated(), $emailSetting);
//
//        return response()->json([
//            'item' => $emailSetting,
//            'additional' => $emailSetting->getAdditionalData(),
//        ]);
//    }
//

//
//    public function copy(EmailSetting $emailSetting): JsonResponse
//    {
//        $emailSettingCopy = $this->emailSettingService->copy($emailSetting);
//
//        return response()->json([
//            'item' => $emailSettingCopy,
//            'additional' => $emailSettingCopy->getAdditionalData(),
//        ]);
//    }
//
}
