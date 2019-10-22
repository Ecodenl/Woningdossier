<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\PicoHelper;
use App\Helpers\RoleHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBuildingFormRequest;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CreateBuildingController extends Controller
{
    /**
     * If a user tries to login and we notice there is no building for that user, show him the page where he can create a new building to his account.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('cooperation.create-building.index');
    }

    /**
     * Store the building and connect it to the user his account.
     *
     * @param CreateBuildingFormRequest $request
     * @param Cooperation               $cooperation
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateBuildingFormRequest $request, Cooperation $cooperation)
    {
        $email = $request->get('email');

        $account = Account::where('email', $email)->first();
        $user = $account->user();

        // a user has to give up his mail and password again, so we can verify he is only creating the building for himself.
        $data = $request->all();

        // now get the picoaddress data.
        $picoAddressData = PicoHelper::getAddressData(
            $data['postal_code'], $data['number']
        );

        $data['bag_addressid'] = isset($picoAddressData['bag_adresid']) ? $picoAddressData ['bag_adresid'] : '';

        $features = new BuildingFeature([
            'surface' => empty($picoAddressData['surface']) ? null : $picoAddressData['surface'],
            'build_year' => empty($picoAddressData['build_year']) ? null : $picoAddressData['build_year'],
        ]);

        $address = new Building($data);
        $address->user()->associate($user)->save();

        $features->building()->associate($address)->save();

        return redirect(route('cooperation.auth.login'))->with('success', __('woningdossier.cooperation.create-building.store.success'));
    }


}
