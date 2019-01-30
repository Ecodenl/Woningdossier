<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Http\Requests\CreateBuildingFormRequest;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CreateBuildingController extends Controller
{
    /**
     * If a user tries to login and we notice there is no building for that user, show him the page where he can create a new building to his account
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
     * @param Cooperation $cooperation
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateBuildingFormRequest $request, Cooperation $cooperation)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        // get the user from the current cooperation
        $user = $cooperation->users()->where('email', $email)->first();

        // if the user already has a building, redirect him to the login page.
        if ($user->buildings()->first() instanceof Building) {
            return redirect(route('cooperation.home'));
        }

        // a user has to give up his mail and password again, so we can verify he is only creating the building for himself.
        if (\Auth::attempt(['email' => $email, 'password' => $password])) {

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

            // after all the data is saved, log the user in.
            \Auth::login($user);

            // now do all the stuff we would normaly do in the login controller
            // we cant query on the Spatie\Role model so we first get the result on the "original model"
            $role = Role::findByName($user->roles->first()->name);

            // get the input source
            $inputSource = $role->inputSource;

            // if there is only one role set for the user, and that role does not have an input source we will set it to resident.
            if (!$role->inputSource instanceof InputSource) {
                $inputSource = InputSource::findByShort('resident');
            }

            // set the required sessions
            HoomdossierSession::setHoomdossierSessions($address, $inputSource, $inputSource, $role);

            // set the redirect url
            if (1 == $user->roles->count()) {
                return redirect(RoleHelper::getUrlByRole($role))->with('success', __('woningdossier.cooperation.create-building.store.success'));
            } else {
                return redirect(url('/admin'))->with('success', __('woningdossier.cooperation.create-building.store.success'));
            }

        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);


    }

    protected function logout(Request $request)
    {
        HoomdossierSession::destroy();

        $this->guard()->logout();

        $request->session()->invalidate();
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
