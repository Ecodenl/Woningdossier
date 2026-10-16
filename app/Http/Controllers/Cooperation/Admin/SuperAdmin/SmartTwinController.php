<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Enums\SmartTwin\EventType;
use App\Http\Controllers\Controller;
use App\Jobs\SmartTwin\ProcessAdviceResults;
use App\Models\Cooperation;
use App\Models\FileStorage;
use App\Models\InputSource;
use App\Services\FileStorageService;
use App\Services\SmartTwin\SmartTwinFileTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Temporary tooling while the mapping is being built. The SmartTwin artifacts (the raw advice JSON,
 * and later the CSV of unmapped fields) are stored per building in FileStorage, and no screen
 * surfaces them. This lets a super admin inspect them across all cooperations, and replay the
 * mapping on a stored response so it can be tweaked and retested without a new scan.
 */
class SmartTwinController extends Controller
{
    public function index(Cooperation $cooperation): View
    {
        $fileStorages = $this->query()
            ->with(['cooperation', 'building', 'fileType', 'inputSource'])
            ->orderByDesc('updated_at')
            ->get();

        return view('cooperation.admin.super-admin.smart-twin.index', compact('fileStorages'));
    }

    public function download(Cooperation $cooperation, int $fileStorageId): StreamedResponse|RedirectResponse
    {
        $fileStorage = $this->find($fileStorageId);

        // The stored name is the same for every building, so prefix it to keep downloads apart
        // when comparing the results of multiple buildings.
        return FileStorageService::download(
            $fileStorage,
            sprintf('%d-%s', $fileStorage->building_id, basename($fileStorage->filename)),
        );
    }

    public function reprocess(Cooperation $cooperation, int $fileStorageId): RedirectResponse
    {
        $fileStorage = $this->find($fileStorageId);
        $eventType = $fileStorage->inputSource instanceof InputSource
            ? EventType::tryFromInputSource($fileStorage->inputSource)
            : null;

        // Only the raw response can be replayed; it's the mapping's input. Anything else (the
        // unmapped CSV) is mapping output, and is rewritten by replaying the response it came from.
        if (SmartTwinFileTypes::ADVICE_RAW !== $fileStorage->fileType->short
            || ! $eventType instanceof EventType
            || is_null($fileStorage->building_id)
        ) {
            return redirect()->back()
                ->with('warning', __('cooperation/admin/super-admin/smart-twin.reprocess.not-supported'));
        }

        ProcessAdviceResults::dispatch($fileStorage->building_id, $eventType);

        return redirect()->back()
            ->with('success', __('cooperation/admin/super-admin/smart-twin.reprocess.success'));
    }

    private function find(int $fileStorageId): FileStorage
    {
        /** @var FileStorage */
        return $this->query()->findOrFail($fileStorageId);
    }

    /**
     * The rows are per building, expire after 30 days and belong to a cooperation other than the
     * one in the super admin's session, so every FileStorage global scope has to come off.
     */
    private function query(): Builder
    {
        return FileStorage::withExpired()
            ->allInputSources()
            ->forAllCooperations()
            ->whereHas('fileType', fn (Builder $query) => $query->whereIn('short', SmartTwinFileTypes::all()));
    }
}
