<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\CourierInfo;

class CourierController extends Controller
{
    public function courierInfo()
    {
        $pageTitle    = "All Courier List";
        $manager = auth()->user();

        $courierInfos = CourierInfo::where(function ($branch) use ($manager) {
            $branch->orWhere('sender_branch_id', $manager->branch_id)
                ->orWhere('receiver_branch_id', $manager->branch_id);
        })
            ->dateFilter()
            ->searchable(['code', 'receiverBranch:name', 'senderCustomer:mobile', 'receiverCustomer:mobile'])
            ->filter(['status', 'receiver_branch_id', 'sender_branch_id']);

        if (request()->payment_status != null) {
            $courierInfos = $courierInfos->whereHas('paymentInfo', function ($payment) {
                if (request()->payment_status != null) {
                    $payment->where('status', request()->payment_status);
                }
            });
        }

        $courierInfos = $courierInfos->orderBy('id', 'DESC')
            ->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')
            ->paginate(getPaginate());

        return view('manager.courier.index', compact('pageTitle', 'courierInfos'));
    }

    public function sentInQueue()
    {
        $pageTitle    = "Sent In Queue List";
        $courierInfos = $this->couriers('queue');
        return view('manager.courier.index', compact('pageTitle', 'courierInfos'));
    }

    public function sentCourier()
    {
        $manager      = auth()->user();
        $pageTitle    = "Sent All Courier List";
        $courierInfos = CourierInfo::where('sender_branch_id', $manager->branch_id)->where('status', '!=', Status::COURIER_QUEUE)->dateFilter()->searchable(['code'])->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return view('manager.courier.index', compact('pageTitle', 'courierInfos'));
    }

    public function deliveryInQueue()
    {
        $pageTitle    = "Delivery In Queue List";
        $courierInfos = $this->couriers('deliveryQueue');
        return view('manager.courier.index', compact('pageTitle', 'courierInfos'));
    }

    public function dispatchCourier()
    {
        $pageTitle    = "Dispatch Courier List";
        $courierInfos = $this->couriers('dispatched');
        return view('manager.courier.index', compact('pageTitle', 'courierInfos'));
    }

    public function delivered()
    {
        $pageTitle    = "Delivered Courier List";
        $courierInfos = $this->couriers('delivered');
        return view('manager.courier.index', compact('pageTitle', 'courierInfos'));
    }

    public function upcoming()
    {
        $pageTitle    = "Upcoming Courier List";
        $courierInfos = $this->couriers('upcoming');
        return view('manager.courier.index', compact('pageTitle', 'courierInfos'));
    }

    protected function couriers($scope = null)
    {
        $user     = auth()->user();
        $couriers = CourierInfo::where(function ($query) use ($user) {
            $query->where('sender_branch_id', $user->branch_id)->orWhere('receiver_branch_id', $user->branch_id);
        });
        if ($scope) {
            $couriers = $couriers->$scope();
        }
        $couriers = $couriers->dateFilter()->searchable(['code'])->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return $couriers;
    }

    public function invoice($id)
    {
        $id                  = decrypt($id);
        $pageTitle           = "Invoice";
        $courierInfo         = CourierInfo::with('products.type.unit', 'paymentInfo', 'senderCustomer', 'receiverCustomer')->findOrFail($id);
        return view('manager.courier.invoice', compact('pageTitle', 'courierInfo'));
    }
}
