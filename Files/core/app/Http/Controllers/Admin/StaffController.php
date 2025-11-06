<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class StaffController extends Controller
{
    public function list()
    {
        $pageTitle = 'All Staff List';
        $staffs = User::searchable(['username', 'email', 'mobile', 'branch:name'])->staff()->with('branch')->paginate(getPaginate());
        return view('admin.staff.index', compact('pageTitle', 'staffs'));
    }
}
