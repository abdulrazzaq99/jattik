<?php

namespace App\Http\Controllers\Admin;

use App\Models\CourierInfo;
use App\Http\Controllers\Controller;

class CourierController extends Controller
{
    public function courierInfo()
    {
        $pageTitle    = "Courier Information";
        $courierInfos = CourierInfo::dateFilter()
            ->searchable(['code', 'receiverBranch:name', 'senderCustomer:mobile', 'receiverCustomer:mobile'])
            ->filter(['status', 'receiver_branch_id', 'sender_branch_id'])
            ->where(function ($q) {
                $q->orWhereHas('paymentInfo', function ($myQuery) {
                    if (request()->payment_status != null) {
                        $myQuery->where('status', request()->payment_status);
                    }
                });
            })
            ->orderBy('id', 'DESC')
            ->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')
            ->paginate(getPaginate());

        return view('admin.courier.index', compact('pageTitle', 'courierInfos'));
    }

    public function courierDetail($id)
    {
        $courierInfo = CourierInfo::with('products.type.unit', 'paymentInfo')->findOrFail($id);
        $pageTitle   = "Courier Detail: " . $courierInfo->code;
        return view('admin.courier.details', compact('pageTitle', 'courierInfo'));
    }

    public function invoice($id)
    {
        $courierInfo = CourierInfo::with('products.type.unit', 'paymentInfo', 'senderCustomer', 'receiverCustomer')->findOrFail($id);
        $pageTitle   = "Invoice";
        return view('admin.courier.invoice', compact('pageTitle', 'courierInfo'));
    }
}
