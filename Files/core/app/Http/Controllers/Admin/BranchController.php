<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $pageTitle = "Manage Branch";
        $branches  = Branch::searchable(['name', 'email', 'phone', 'address'])->orderBy('name', 'ASC')->paginate(getPaginate());
        return view('admin.branch.index', compact('pageTitle', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:40',
            'email'   => 'required|email|max:40',
            'phone'   => 'required|max:40',
            'address' => 'required|max:255',
        ]);

        if ($request->id) {
            $branch  = Branch::findOrFail($request->id);
            $message = "Branch updated successfully";
        } else {
            $branch  = new Branch();
            $message = "Branch added successfully";
        }

        $branch->name    = $request->name;
        $branch->email   = $request->email;
        $branch->phone   = $request->phone;
        $branch->address = $request->address;
        $branch->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Branch::changeStatus($id);
    }
}
