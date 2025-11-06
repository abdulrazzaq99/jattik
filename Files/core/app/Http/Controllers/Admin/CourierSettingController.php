<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CourierPayment;
use App\Models\Type;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourierSettingController extends Controller
{
    public function unitIndex()
    {
        $pageTitle = "Manage Unit";
        $units     = Unit::orderBy('name')->paginate(getPaginate());
        return view('admin.unit.unit', compact('pageTitle', 'units'));
    }

    public function unitStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        if ($request->id) {
            $unit    = Unit::findOrFail($request->id);
            $message = 'Unit updated successfully';
        } else {
            $unit = new Unit();
        }
        $unit->name = $request->name;
        $unit->save();
        $notify[] = ['success', isset($message) ? $message : 'Unit added successfully'];
        return back()->withNotify($notify);
    }

    public function typeIndex()
    {
        $pageTitle = "Manage Courier Type";
        $units     = Unit::active()->orderBy('name')->get();
        $types     = Type::orderBy('name')->with('unit')->paginate(getPaginate());
        return view('admin.unit.type', compact('pageTitle', 'types', 'units'));
    }

    public function typeStore(Request $request)
    {
        $request->validate([
            'unit'  => 'required|exists:units,id',
            'name'  => 'required',
            'price' => 'required|gt:0|numeric',
        ]);

        if ($request->id) {
            $unit    = Type::findOrFail($request->id);
            $message = "Type updated successfully";
        } else {
            $unit = new Type();
        }
        $unit->name    = $request->name;
        $unit->unit_id = $request->unit;
        $unit->price   = $request->price;
        $unit->save();
        $notify[] = ['success', isset($message) ? $message  : 'Type added successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Unit::changeStatus($id);
    }

    public function typeStatus($id)
    {
        return Type::changeStatus($id);
    }
    public function branchIncome()
    {
        $pageTitle     = "Branch Income History";
        $branches      = Branch::active()->latest('id')->get();
        $branchIncomes = CourierPayment::where('branch_id', '!=', 0)->dateFilter()->filter(['branch_id'])->where('status', Status::PAID)->select(DB::raw("*,SUM(final_amount) as totalAmount"))
            ->groupBy('branch_id')->with('branch')->paginate(getPaginate());
        return view('admin.courier.income', compact('pageTitle', 'branchIncomes', 'branches'));
    }
}
