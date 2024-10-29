<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Deprecation\DeprecationLogger;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatRequest;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Services\PrivateMessageService;
use App\Services\PrivateMessageViewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessagesController extends Controller
{
    public function index(Cooperation $cooperation): RedirectResponse
    {
        DeprecationLogger::log(__METHOD__ . ' used!');
        return redirect(route('cooperation.my-account.messages.edit'));
    }

    public function edit(Cooperation $cooperation): RedirectResponse|View
    {
        //TODO: Should we redirect if the current user has a role that should resolve to the admin according to
        // the navbar / Messages Livewire component?
        $buildingId = HoomdossierSession::getBuilding();
        $privateMessages = PrivateMessage::public()
            ->conversation($buildingId)
            ->get();

        // TODO: See if we can deprecate this
        // if no private message exist redirect them to the conversation request create
        // currently (as of 30-10-2020), this shouldnt be needed anymore.
        // this is on register a private message will be created.
        if (! $privateMessages->first() instanceof PrivateMessage) {
            return redirect()->route('cooperation.conversation-requests.index', ['requestType' => PrivateMessageService::REQUEST_TYPE_COACH_CONVERSATION]);
        }

        $this->authorize('update', $privateMessages->first());

        $groupParticipants = PrivateMessage::getGroupParticipants($buildingId);

        // Only residents read this box
        $resident = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        PrivateMessageViewService::markAsReadByUser($privateMessages, Hoomdossier::user(), $resident);
        //PrivateMessageViewService::setRead($privateMessages);

        return view('cooperation.my-account.messages.edit', compact('privateMessages', 'buildingId', 'groupParticipants'));
    }

    public function store(ChatRequest $request): RedirectResponse
    {
        PrivateMessageService::create($request);

        return redirect()->back();
    }
}
