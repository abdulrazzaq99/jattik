<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class CustomerController extends Controller
{
    public function customerList()
    {
        $pageTitle = 'All Customer List';
        $customers = Customer::searchable(['email', 'mobile'])->orderBy('id', 'desc')->paginate(getPaginate());
        $columns   = ['id', 'firstname', 'lastname', 'email', 'mobile', 'address', 'city', 'state', 'created_at', 'updated_at'];
        return view('admin.customer.index', compact('pageTitle', 'customers', 'columns'));
    }

    public function importCustomers(Request $request)
    {
        try {
            $import   = importFileReader($request->file, ['firstname', 'lastname', 'email', 'mobile', 'address', 'city', 'state'], ['email', 'mobile']);
            $notify[] = ['success', @$import->notify['message']];
            return back()->withNotify($notify);
        } catch (Exception $ex) {
            $notify[] = ['error', $ex->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function exportCustomers(Request $request)
    {
        $customer                = new Customer();
        $customer->fileName      = 'all_customer.csv';
        $customer->exportColumns = $request->columns;
        $customer->exportItem    = $request->export_item;
        $customer->orderBy       = $request->order_by ? 'asc' : 'desc';
        return $customer->export();
    }
}
