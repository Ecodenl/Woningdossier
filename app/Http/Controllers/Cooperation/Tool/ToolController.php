<?php

namespace App\Http\Controllers\Cooperation\Tool;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Step;
use Illuminate\Http\Request; use App\Scopes\GetValueScope;

class ToolController extends Controller
{
    protected $step;

    public function __construct(Request $request)
    {
        $slug = str_replace('/tool/', '', $request->getRequestUri());
        $this->step = Step::where('slug', $slug)->first();
    }

    /**
     * Redirect to the general data step since the tool view has no content.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {
        $cooperation = Cooperation::find(\Session::get('cooperation'));

        return redirect(route('cooperation.tool.general-data.index', ['cooperation' => $cooperation]));
    }

    public function csvToArray($file = '', $delimiter = ',')
    {

        $header = null;
        $data = [];

        if (($handle = fopen($file, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false)
            {
                if(!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    public function import()
    {
        $importFile = asset('translations-import.csv');

        $csvData = $this->csvToArray($importFile);

        foreach ($csvData as $data) {
            if ($data['short'] == "") {
                dd($data);
            }
        }

        dd($csvData);

    }
}
