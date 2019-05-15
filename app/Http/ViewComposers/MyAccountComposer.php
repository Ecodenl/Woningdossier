<?php

namespace App\Http\ViewComposers;

use App\Models\PrivateMessageView;
use Illuminate\View\View;

class MyAccountComposer
{
    public function create(View $view)
    {
        $view->with('myUnreadMessagesCount', PrivateMessageView::getTotalUnreadMessagesForCurrentRole());
    }
}
