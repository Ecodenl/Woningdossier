<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Helpers\MediaHelper;
use App\Helpers\Models\CooperationSettingHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\SettingsFormRequest;
use App\Models\Cooperation;
use Illuminate\Http\UploadedFile;
use Plank\Mediable\Facades\MediaUploader;
use App\Models\Media;

class SettingsController extends Controller
{
    public function index(Cooperation $cooperation, Cooperation $cooperationToManage): View
    {
        $cooperationSettings = $cooperationToManage->cooperationSettings;

        return view('cooperation.admin.cooperation.cooperation-admin.settings.index', compact('cooperationSettings', 'cooperationToManage'));
    }

    public function store(SettingsFormRequest $request, Cooperation $cooperation, Cooperation $cooperationToManage): RedirectResponse
    {
        $cooperationSettings = $request->validated()['cooperation_settings'];
        CooperationSettingHelper::syncSettings($cooperationToManage, $cooperationSettings);

        $tags = MediaHelper::getFillableTagsForClass(Cooperation::class);
        foreach ($tags as $tag) {
            $file = $request->file('medias.' . $tag);
            $media = $cooperationToManage->firstMedia($tag);
            if ($file instanceof UploadedFile) {
                // Check if media for this tag already exists
                if ($media instanceof Media) {
                    // We delete it so we can make place for new media;
                    // We _could_ use replace(), but this doesn't update the file names.
                    $media->delete();
                }

                // Upload the new media, replace file if it already exists
                $media = MediaUploader::fromSource($file)
                    ->onDuplicateIncrement()
                    ->toDestination('uploads', $cooperationToManage->slug)
                    ->upload();

                $cooperationToManage->syncMedia($media, [$tag]);
            } else {
                // Check if user has removed file.
                if ($media instanceof Media && is_null($request->input("medias.{$tag}_current"))) {
                    $media->delete();
                }
            }
        }

        return redirect()->back()
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/settings.store.success'));
    }
}
