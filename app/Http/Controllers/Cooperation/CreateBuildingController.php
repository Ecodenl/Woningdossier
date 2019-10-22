<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
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

        $address = $this->getAddressData($data['postal_code'], $data['number'], $data['addressid']);
        $data['bag_addressid'] = isset($address['bag_adresid']) ? $address['bag_adresid'] : '';

        $features = new BuildingFeature([
            'surface' => array_key_exists('adresopp', $address) ? $address['adresopp'] : null,
            'build_year' => array_key_exists('bouwjaar', $address) ? $address['bouwjaar'] : null,
        ]);

        $address = new Building($data);
        $address->user()->associate($user)->save();

        $features->building()->associate($address)->save();

        return redirect(route('cooperation.auth.login'))->with('success', __('woningdossier.cooperation.create-building.store.success'));
    }



    protected function getAddressData($postalCode, $number, $pointer = null)
    {
        \Log::debug($postalCode.' '.$number.' '.$pointer);
        /** @var PicoClient $pico */
        $pico = app()->make('pico');
        $postalCode = str_replace(' ', '', trim($postalCode));

        $response = $pico->bag_adres_pchnr(['query' => ['pc' => $postalCode, 'hnr' => $number]]);

        if (! is_null($pointer)) {
            foreach ($response as $addrInfo) {
                if (array_key_exists('bag_adresid', $addrInfo) && $pointer == md5($addrInfo['bag_adresid'])) {
                    //$data['bag_addressid'] = $addrInfo['bag_adresid'];
                    \Log::debug(json_encode($addrInfo));

                    return $addrInfo;
                }
            }

            return [];
        }

        return $response;
    }
}
