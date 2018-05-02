<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MeasureController extends Controller
{
    public function index()
    {

        // Create a new collection for the files
        $files = collect();
        // Go through the dir and get the filepath
        foreach (Storage::files('public/hoomdossier-assets') as $filePath) {
            // Change public to storage
            $file = str_replace('public', 'storage', $filePath);
            // Put them inside the collection
            $files->push($file);
        }

        return view('cooperation.measure.index', compact('files'));
    }
}
