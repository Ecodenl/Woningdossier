<?php

namespace App\Http\Controllers\Cooperation;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\User;
use App\Services\DumpService;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Cooperation $cooperation)
    {
        $users = User::forAllCooperations()->findMany([1, 2, 5, 9, 12]);
        foreach ($users as $user) {
            $t = [];
            $calculateDataByStep = DumpService::getCalculateData($user, InputSource::findByShort('resident'));
            foreach ($calculateDataByStep as $step => $calculateDataBySubStep) {
//                $t[] = Str::studly($step.'Helper');
                foreach ($calculateDataBySubStep as $subStep => $calculateData) {

                }
            }
        }
        dd($t);

        return view('cooperation.home.index');
    }
}
