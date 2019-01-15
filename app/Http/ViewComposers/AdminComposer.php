<?php

namespace App\Http\ViewComposers;

use App\Models\InputSource;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use Illuminate\View\View;

class AdminComposer
{
    public function create(View $view)
    {
        $view->with('myUnreadMessagesCount', PrivateMessageView::getTotalUnreadMessages());
    }
}
