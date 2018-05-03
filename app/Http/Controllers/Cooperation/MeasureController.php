<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MeasureController extends Controller
{
    public function index()
    {

    	// (parts of file => icons)
    	$categorizedFiles = [
			'begrippenlijst' => 'general-data.png',
		    'bodemisolatie' => 'floor-insulation.png',
		    'vloerisolatie' => 'floor-insulation.png',
		    'cv ketel' => 'high-efficiency-boiler.png',
		    'dakisolatie' => 'roof-insulaton.png',
		    'wtw' => 'heater.png',
		    'gevelisolatie' => 'wall-insulation.png',
			'spouwisolatie' => 'wall-insulation.png',
		    'glasisolatie' => 'insulated-glazing.png',
		    'kierdichting' => 'insulated-glazing.png',
		    'ventilatie' => 'ventilation-information.png',
		    'warmtepomp' => 'heat-pump.png',
		    'zonneboiler' => 'heater.png',
		    'zonnepanelen' => 'solar-panels.png',
	    ];

	    // allowed extensions / files
	    $allowedExtensions = ['pdf'];

        // Create a new collection for the files
        $files = collect();
        // Go through the dir and get the filepath
        foreach (Storage::files('public/hoomdossier-assets') as $filePath) {

            // get the extenstion of the file
            $fileExtension = pathinfo($filePath)['extension'];
            // don't display the Info- prefixed files. Only measures.
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
