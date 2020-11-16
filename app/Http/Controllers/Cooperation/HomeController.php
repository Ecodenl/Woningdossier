<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\Hoomdossier;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\User;
use App\Services\DumpService;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Cooperation $cooperation)
    {
        $user = Hoomdossier::user();
//        $users = User::forAllCooperations()->findMany([1, 2, 5, 9, 12]);
//        foreach ($users as $user) {
//            $t = [];
//            foreach ($calculateDataByStep as $step => $calculateDataBySubStep) {
////                $t[] = Str::studly($step.'Helper');
//            $calculateDataByStep = DumpService::getCalculateData($user, InputSource::findByShort('resident'));
//                foreach ($calculateDataBySubStep as $subStep => $calculateData) {
//
//                }
//            }
//        }

        return view('cooperation.home.index');
    }
}
