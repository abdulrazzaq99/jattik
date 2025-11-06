<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;

class StaffTicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        $this->user = auth()->user();
        $this->redirectLink = 'staff.ticket.view';
        $this->userType     = 'staff';
        $this->column       = 'user_id';
        $this->userGuard    = 'staff';
    }
}
