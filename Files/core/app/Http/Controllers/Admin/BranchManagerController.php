<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BranchManagerController extends Controller
{
    public function index()
    {
        $pageTitle      = "All Branch Manager";
        $branchManagers = User::searchable(['username', 'email', 'mobile', 'branch:name'])->manager()->latest('id')->with('branch')->paginate(getPaginate());
        return view('admin.manager.index', compact('pageTitle', 'branchManagers'));
    }

    public function create()
    {
        $pageTitle = "Add Branch Manager";
        $branches  = Branch::active()->orderBy('name')->get();
        return view('admin.manager.create', compact('pageTitle', 'branches'));
    }

    public function store(Request $request, $id = 0)
    {
        $validation = $id ? 'nullable' : 'required';

        $request->validate([
            'branch'    => 'required|exists:branches,id',
            'firstname' => 'required|max:40',
            'lastname'  => 'required|max:40',
            'password' => [$validation, 'confirmed', 'min:4'],
            'email'    => 'required|email|max:40|unique:users,email,' . $id,
            'username' => 'required|max:40|unique:users,username,' . $id,
            'mobile'   => 'required|max:40|unique:users,mobile,' . $id,
        ]);

        $branch = Branch::where('id', $request->branch)->first();

        if ($branch->status == Status::NO) {
            $notify[] = ['error', 'This branch is inactive'];
            return back()->withNotify($notify)->withInput();
        }

        if ($id) {
            $manager = User::findOrFail($id);
            $message = "Manager updated successfully";
        } else {
            $manager           = new User();
            $manager->password = Hash::make($request->password);
        }

        if ($manager->branch_id != $request->branch) {
            $hasManager = User::manager()->where('branch_id', $request->branch)->exists();
            if ($hasManager) {
                $notify[] = ['error', 'This branch has already a manager'];
                return back()->withNotify($notify)->withInput();
            }
        }

        $manager->branch_id = $request->branch;
        $manager->firstname = $request->firstname;
        $manager->lastname  = $request->lastname;
        $manager->username  = $request->username;
        $manager->email     = $request->email;
        $manager->mobile    = $request->mobile;
        $manager->user_type = "manager";
        $manager->save();

        if (!$id) {
            notify($manager, 'MANAGER_CREATE', [
                'username' => $manager->username,
                'email'    => $manager->email,
                'password' => $request->password,
            ]);
        }

        $notify[] = ['success', isset($message) ? $message : 'Manager added successfully'];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = "Update Branch Manager";
        $branches  = Branch::active()->orderBy('name')->get();
        $manager   = User::findOrFail($id);
        return view('admin.manager.edit', compact('pageTitle', 'branches', 'manager'));
    }

    public function staffList($branchId)
    {
        $pageTitle = "Staff List";
        $staffs = User::searchable(['username', 'email', 'mobile', 'branch:name'])->staff()->where('branch_id', $branchId)->with('branch')->paginate(getPaginate());
        return view('admin.manager.staff', compact('pageTitle', 'staffs'));
    }

    public function status($id)
    {
        return User::changeStatus($id);
    }

    public function login($id)
    {
        User::manager()->where('id', $id)->firstOrFail();
        auth()->loginUsingId($id);
        return to_route('manager.dashboard');
    }

    public function staffLogin($id)
    {
        User::staff()->where('id', $id)->firstOrFail();
        auth()->loginUsingId($id);
        return to_route('staff.dashboard');
    }

    public function branchManager($id)
    {
        $branch         = Branch::findOrFail($id);
        $pageTitle      = $branch->name . " Manager List";
        $branchManagers = User::manager()->where('branch_id', $id)->orderBy('id', 'DESC')->with('branch')->paginate(getPaginate());
        return view('admin.manager.index', compact('pageTitle', 'branchManagers'));
    }
}
