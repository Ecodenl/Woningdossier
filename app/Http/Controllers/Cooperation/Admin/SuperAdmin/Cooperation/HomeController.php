<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin\Cooperation;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Cooperation $currentCooperation, Cooperation $cooperationToManage)
    {
        $breadcrumbs = [
            [
                'route' => 'cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index',
                'url' => route('cooperation.admin.super-admin.cooperations.cooperation-to-manage.home.index', [$currentCooperation, $cooperationToManage]),
                'name' => $cooperationToManage->name,
            ]
        ];

        $cooperationAdminCount = $cooperationToManage->users()->role('cooperation-admin')->count();
        $coordinatorAdminCount = $cooperationToManage->users()->role('coordinator')->count();
        $userCount = $cooperationToManage->users()->count();

        // because relationship counting aint working.
        $buildingCount = \DB::table('cooperations')
            ->where('slug', $cooperationToManage->slug)
            ->leftJoin('cooperation_user', 'cooperation_user.cooperation_id', '=', 'cooperations.id')
            ->leftJoin('users', 'cooperation_user.user_id', '=', 'users.id')
            ->leftJoin('buildings', 'buildings.user_id', '=', 'users.id')
            ->select('buildings.*')->count();

        return view('cooperation.admin.super-admin.cooperations.home.index', compact(
            'cooperationCount', 'userCount', 'buildingCount', 'cooperationAdminCount', 'coordinatorAdminCount',
            'breadcrumbs'
        ));
    }
}
