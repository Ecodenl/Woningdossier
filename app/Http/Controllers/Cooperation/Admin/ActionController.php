<?php

namespace App\Http\Controllers\Cooperation\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Cooperation;

class ActionController extends Controller
{
    public function verifyEmail(Cooperation $cooperation, Account $account): RedirectResponse
    {
        $this->authorize('verifyEmail', $account);
        $account->update(['email_verified_at' => now()]);

        return redirect()->back()
            ->with('success', __('cooperation/admin/cooperation/residents.index.table.columns.email-verified'));
    }
}
