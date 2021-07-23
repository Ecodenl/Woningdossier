<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\CooperationAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Cooperation\CooperationAdmin\SettingsFormRequest;
use App\Models\Cooperation;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Plank\Mediable\Facades\MediaUploader;

class SettingsController extends Controller
{
    public function index()
    {
        return view('cooperation.admin.cooperation.cooperation-admin.settings.index');
    }

    public function store(SettingsFormRequest $request, Cooperation $cooperation) {
        $tags = ['logo', 'background'];
        foreach ($tags as $tag) {
            $file = $request->file('medias.'.$tag);

            if ($file instanceof UploadedFile) {
                $media = MediaUploader::fromSource($file)
                    ->onDuplicateReplace()
                    ->toDestination('uploads', $cooperation->slug)
                    ->upload();

                $cooperation->syncMedia($media, [$tag]);
            }
        }

        return redirect()->back()
            ->with('success', __('cooperation/admin/cooperation/cooperation-admin/settings.store.success'));
    }
}
