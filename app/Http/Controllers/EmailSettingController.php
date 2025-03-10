<?php

namespace App\Http\Controllers;

use App\DataTables\EmailSettings\EmailSettingDataTable;
use App\Http\Requests\EmailSettingRequest;
use App\Models\EmailSetting;
use App\Services\EmailSettingService;
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
                'translate' => '$this->mainTranslate',
            ],
            'data_table' => (new EmailSettingDataTable())->getData(),
        ]);
    }

    public function create(): Response
    {
        return inertia('modules/email-settings/create', [
            'options' => [
                'types' => collect(EmailSetting::getTypes())
                    ->map(function ($type) {
                        return ['code' => $type, 'name' => $type];
                    }),
                'protocols' => collect(EmailSetting::getProtocols())
                    ->map(function ($type) {
                        return ['code' => $type, 'name' => $type];
                    }),
            ],
        ]);
    }

    public function store(EmailSettingRequest $request): Response
    {
        $emailSetting = $this->emailSettingService->store($request->validated());

        return inertia('modules/email-settings/index', [
            'main' => [
                'translate' => '$this->mainTranslate',
            ],
            'data_table' => (new EmailSettingDataTable())->getData(),
        ]);
    }

    public function show(EmailSetting $emailSetting): Response
    {
        return inertia('modules/email-settings/edit', [
            'item' => $this->emailSettingService->show($emailSetting),
            'options' => [
                'types' => collect(EmailSetting::getTypes())
                    ->map(function ($type) {
                        return ['code' => $type, 'name' => $type];
                    }),
                'protocols' => collect(EmailSetting::getProtocols())
                    ->map(function ($type) {
                        return ['code' => $type, 'name' => $type];
                    }),
            ],
        ]);
    }

    public function update(EmailSettingRequest $request, EmailSetting $emailSetting): Response
    {
        $this->emailSettingService->update($request->validated(), $emailSetting);

        return inertia('modules/email-settings/edit', [
            'item' => $this->emailSettingService->show($emailSetting),
            'options' => [
                'types' => collect(EmailSetting::getTypes())
                    ->map(function ($type) {
                        return ['code' => $type, 'name' => $type];
                    }),
                'protocols' => collect(EmailSetting::getProtocols())
                    ->map(function ($type) {
                        return ['code' => $type, 'name' => $type];
                    }),
            ],
        ]);
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
//    public function destroy(EmailSetting $emailSetting): JsonResponse
//    {
//        $emailSetting->delete();
//        return response()->json([], 204);
//    }
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
//    public function checkConnection(EmailSetting $emailSetting): JsonResponse
//    {
//        $this->emailSettingService->checkConnection($emailSetting);
//
//        return response()->json([
//            'message' => 'success!',
//        ]);
//    }
}
