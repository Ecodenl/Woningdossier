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

            // get the extenstion of the file
            $fileExtension = pathinfo($filePath)['extension'];
            // allowed extensions / files
            $allowedExtensions = ['pdf', 'docx'];
            if (in_array($fileExtension, $allowedExtensions)) {
                // Change public to storage
                $file = str_replace('public', 'storage', $filePath);
                // push them inside a collection
                $files->push($file);
            }
        }

        return view('cooperation.measures.index', compact('files'));
    }
}
