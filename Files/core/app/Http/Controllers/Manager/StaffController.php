<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function create()
    {
        $pageTitle = "Add Staff";
        return view('manager.staff.create', compact('pageTitle'));
    }

    public function index()
    {
        $pageTitle = "All Staff";
        $manager   = auth()->user();
        $staffs    = User::searchable(['username', 'email', 'mobile'])->where(function ($query) use ($manager) {
            $query->staff()->where('branch_id', $manager->branch_id);
        })->with('branch')->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('manager.staff.index', compact('pageTitle', 'staffs'));
    }

    public function edit($id)
    {
        $pageTitle = "Staff Update";
        $manager   = auth()->user();
        $staff     = User::where('id', $id)->where('branch_id', $manager->branch_id)->firstOrFail();
        return view('manager.staff.edit', compact('pageTitle', 'staff'));
    }

    public function store(Request $request)
    {
        $manager = auth()->user();
        $validationRule = [
            'firstname' => 'required|max:40',
            'lastname'  => 'required|max:40',
        ];

        if ($request->id) {
            $validationRule = array_merge($validationRule, [
                'email'    => 'required|email|max:40|unique:users,email,' . $request->id,
                'username' => 'required|max:40|unique:users,username,' . $request->id,
                'mobile'   => 'required|max:40|unique:users,mobile,' . $request->id,
                'password' => 'nullable|confirmed|min:4',
            ]);
        } else {
            $validationRule = array_merge($validationRule, [
                'email'    => 'required|email|max:40|unique:users',
                'username' => 'required|max:40|unique:users',
                'mobile'   => 'required|max:40|unique:users',
                'password' => 'nullable|confirmed|min:4',
            ]);
        }

        $request->validate($validationRule);

        $staff = new User();

        if ($request->id) {
            $staff   = User::where('id', $request->id)->where('branch_id', $manager->branch_id)->firstOrFail();
            $message = "Staff updated successfully";
        }

        $staff->branch_id = $manager->branch_id;
        $staff->firstname = $request->firstname;
        $staff->lastname  = $request->lastname;
        $staff->username  = $request->username;
        $staff->email     = $request->email;
        $staff->mobile    = $request->mobile;
        $staff->user_type = "staff";
        $staff->password  = Hash::make($request->password);
        $staff->save();

        if (!$request->id) {
            notify($staff, 'STAFF_CREATE', [
                'username' => $staff->username,
                'email'    => $staff->email,
                'password' => $request->password,
            ]);
        }

        $notify[] = ['success', isset($message) ? $message : 'Staff added successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return User::changeStatus($id);
    }
}
