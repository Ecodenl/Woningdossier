<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Step;
use Illuminate\Support\Facades\Storage;

class MeasureController extends Controller
{
    public function index()
    {
    	// (parts of file => icons)
    	$categorizedFiles = [
			'begrippenlijst' => 'general-data',
		    'bodemisolatie' => 'floor-insulation',
		    'vloerisolatie' => 'floor-insulation',
		    'cv-ketel' => 'high-efficiency-boiler',
		    'dakisolatie' => 'roof-insulation',
		    'wtw' => 'heater',
		    'gevelisolatie' => 'wall-insulation',
			'spouwisolatie' => 'wall-insulation',
		    'glasisolatie' => 'insulated-glazing',
		    'kierdichting' => 'insulated-glazing',
		    'ventilatie' => 'ventilation-information',
		    'warmtepomp' => 'heat-pump',
		    'zonneboiler' => 'heater',
		    'zonnepanelen' => 'solar-panels',
	    ];

	    // allowed extensions / files
	    $allowedExtensions = ['pdf'];
	    $start = 'Maatregelblad_';

        // Create a new collection for the files
        //$files = collect();
	    $files = [];

	    $steps = Step::orderBy('order')->pluck('slug');
	    $steps = array_flip($steps->toArray());

        // Go through the dir and get the filepath
        foreach (Storage::files('public/hoomdossier-assets') as $filePath) {

            // get the extenstion of the file
            $fileExtension = pathinfo($filePath)['extension'];
            // don't display the Info- prefixed files. Only measures.
            if (in_array($fileExtension, $allowedExtensions) && stristr($filePath, $start) !== false) {

            	// Change public to storage
                $file = str_replace('public', 'storage', $filePath);
                foreach($categorizedFiles as $part => $categoryImage){
                	if (stristr($filePath, $part) !== false){
                		$image = $categoryImage;
	                }
                }
                if(!array_key_exists($image, $files)){
                	$files[$image] = [];
                }
                $files[$image][]= $file;

                // push them inside a collection
                //$files->push($fileData);
            }
        }

        uksort($files, function($fileA, $fileB) use($steps){
        	$indexA = 99;
        	$indexB = 99;
        	if(array_key_exists($fileA, $steps)){
        		$indexA = $steps[$fileA];
	        }
	        if (array_key_exists($fileB, $steps)){
        		$indexB = $steps[$fileB];
	        }
	        if ($indexA == $indexB){
        		return 0;
	        }
	        if ($indexA < $indexB){
        		return -1;
	        }
	        return 1;
        });

        return view('cooperation.measures.index', compact('files'));
    }
}
