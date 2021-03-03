<?php

namespace App\Http\ViewComposers;

use App\Helpers\Models\PrivateMessageViewHelper;
use Illuminate\View\View;

class AdminComposer
{
    public function create(View $view)
    {
        $view->with('myUnreadMessagesCount', PrivateMessageViewHelper::getTotalUnreadMessagesForCurrentRole());
    }
}
