<?php

namespace App\Http\Controllers\Staff;

use Carbon\Carbon;
use App\Models\Type;
use App\Models\Branch;
use App\Models\Customer;
use App\Constants\Status;
use App\Models\CourierInfo;
use Illuminate\Http\Request;
use App\Models\CourierPayment;
use App\Models\CourierProduct;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CourierController extends Controller
{
    public function create()
    {
        $pageTitle = 'Courier Send';
        $branches = Branch::active()->where('id', '!=', auth()->user()->branch_id)->orderBy('name')->get();
        $types = Type::active()->with('unit')->orderBy('name')->get();
        return view('staff.courier.index', compact('pageTitle', 'branches', 'types'));
    }

    public function store(Request $request)
    {
        $this->validator($request);

        $senderCustomer = Customer::where('email', $request->sender_customer_email)->where('mobile', $request->sender_customer_phone)->first();

        if (!$senderCustomer) {
            $senderCustomer = $this->customerInfoUpdate($request, 'sender');
        }

        $receiverCustomer = Customer::where('email', $request->receiver_customer_email)->where('mobile', $request->receiver_customer_phone)->first();
        if (!$receiverCustomer) {
            $receiverCustomer = $this->customerInfoUpdate($request, 'receiver');
        }

        $sender                      = auth()->user();
        $courier                     = new CourierInfo();
        $courier->invoice_id         = getTrx();
        $courier->code               = getTrx();
        $courier->sender_branch_id   = $sender->branch_id;
        $courier->sender_staff_id    = $sender->id;

        $courier->receiver_branch_id = $request->branch;
        $courier->estimate_date      = $request->estimate_date;

        $courier->sender_customer_id = $senderCustomer->id;
        $courier->receiver_customer_id = $receiverCustomer->id;

        $courier->save();

        $subTotal = 0;

        $data = [];
        foreach ($request->items as $item) {
            $courierType = Type::where('id', $item['type'])->first();
            if (!$courierType) {
                continue;
            }
            $price = $courierType->price * $item['quantity'];
            $subTotal += $price;

            $data[] = [
                'courier_info_id' => $courier->id,
                'courier_type_id' => $courierType->id,
                'qty'             => $item['quantity'],
                'parcel_name'     => $item['name'] ?? null,
                'fee'             => $price,
                'type_price'      => $courierType->price,
                'created_at'      => now(),
            ];
        }

        CourierProduct::insert($data);

        $discount                        = $request->discount ?? 0;
        $discountAmount                  = ($subTotal / 100) * $discount;
        $totalAmount                     = $subTotal - $discountAmount;

        $courierPayment                  = new CourierPayment();
        $courierPayment->courier_info_id = $courier->id;
        $courierPayment->amount          = $subTotal;
        $courierPayment->discount        = $discountAmount;
        $courierPayment->final_amount    = $totalAmount;
        $courierPayment->percentage      = $request->discount;
        $courierPayment->status          = $request->payment_status;
        $courierPayment->save();

        if ($courierPayment->status == Status::PAID) {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $sender->id;
            $adminNotification->title     = 'Courier Payment ' . $sender->username;
            $adminNotification->click_url = urlPath('admin.courier.info.details', $courier->id);
            $adminNotification->save();
        }

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $sender->id;
        $adminNotification->title     = 'New courier created to' . $sender->username;
        $adminNotification->click_url = urlPath('admin.courier.info.details', $courier->id);
        $adminNotification->save();

        $notify[] = ['success', 'Courier added successfully'];
        return to_route('staff.courier.invoice', encrypt($courier->id))->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $this->validator($request);

        $sender                      = auth()->user();
        $courier                     = CourierInfo::findOrFail($id);
        $courier->invoice_id         = getTrx();
        $courier->code               = getTrx();
        $courier->sender_branch_id   = $sender->branch_id;
        $courier->sender_staff_id    = $sender->id;

        $senderCustomer = Customer::findOrFail($courier->sender_customer_id);
        $this->customerInfoUpdate($senderCustomer, $request, 'sender');

        $receiverCustomer = Customer::findOrFail($courier->receiver_customer_id);
        $this->customerInfoUpdate($receiverCustomer, $request, 'receiver');

        $courier->receiver_branch_id = $request->branch;
        $courier->estimate_date      = $request->estimate_date;
        $courier->save();

        CourierProduct::where('courier_info_id', $id)->delete();

        $subTotal = 0;
        $data = [];
        foreach ($request->items as $item) {
            $courierType = Type::where('id', $item['type'])->first();
            if (!$courierType) {
                continue;
            }
            $price     = $courierType->price * $item['quantity'];
            $subTotal += $price;

            $data[] = [
                'courier_info_id' => $courier->id,
                'courier_type_id' => $courierType->id,
                'qty'             => $item['quantity'],
                'parcel_name'     => $item['name'] ?? null,
                'fee'             => $price,
                'type_price'      => $courierType->price,
                'created_at'      => now(),
            ];
        }
        CourierProduct::insert($data);

        $discount = $request->discount ?? 0;
        $discountAmount = ($subTotal / 100) * $discount;
        $totalAmount = $subTotal - $discountAmount;

        $user = auth()->user();

        $courierPayment               = CourierPayment::where('courier_info_id', $courier->id)->first();
        $courierPayment->amount       = $subTotal;
        $courierPayment->discount     = $discountAmount;
        $courierPayment->final_amount = $totalAmount;
        $courierPayment->percentage   = $request->discount;
        $courierPayment->status       = $request->payment_status;
        $courierPayment->save();

        if ($request->payment_status == Status::PAID) {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $sender->id;
            $adminNotification->title     = $courier->code . ' Courier Payment Updated  by ' . $user->username;
            $adminNotification->click_url = urlPath('admin.courier.info.details', $courier->id);
            $adminNotification->save();
        } else {
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $sender->id;
            $adminNotification->title     = $courier->code . ' Courier update by ' . $user->username;
            $adminNotification->click_url = urlPath('admin.courier.info.details', $courier->id);
            $adminNotification->save();
        }

        $notify[] = ['success', 'Courier updated successfully'];
        return to_route('staff.courier.invoice', encrypt($courier->id))->withNotify($notify);
    }

    public function invoice($id)
    {
        $pageTitle = 'Invoice';
        $courierInfo = CourierInfo::with('products.type.unit', 'paymentInfo', 'senderCustomer', 'receiverCustomer')->findOrFail(decrypt($id));
        return view('staff.invoice', compact('pageTitle', 'courierInfo'));
    }

    public function edit($id)
    {
        $pageTitle   = 'Edit Courier';
        $id          = decrypt($id);
        $branches    = Branch::active()->where('id', '!=', auth()->user()->branch_id)->orderBy('name')->get();
        $types       = Type::active()->with('unit')->orderBy('name')->get();
        $user        = auth()->user();
        $courierInfo = CourierInfo::with('products.type', 'paymentInfo', 'senderCustomer', 'receiverCustomer')->where('sender_branch_id', $user->branch_id)->where('id', $id)->firstOrFail();

        if ($courierInfo->status != Status::COURIER_QUEUE) {
            $notify[] = ['error', "You can update only sent in queue courier."];
            return back()->with($notify);
        }
        return view('staff.courier.edit', compact('pageTitle', 'courierInfo', 'types', 'branches'));
    }

    public function sentQueue()
    {
        $pageTitle    = 'Sent In Queue';
        $user         = auth()->user();
        $courierLists = CourierInfo::dateFilter()->searchable(['code', 'receiverBranch:name'])->where('sender_branch_id', $user->branch_id)->where('status', Status::COURIER_QUEUE)->orderBy('id', 'DESC')
            ->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return view('staff.courier.sentQueue', compact('pageTitle', 'courierLists'));
    }

    public function courierDispatch()
    {
        $pageTitle    = 'Dispatch Courier';
        $user         = auth()->user();
        $courierLists = CourierInfo::dateFilter()->searchable(['code', 'receiverBranch:name'])->where('sender_branch_id', $user->branch_id)->where('status', Status::COURIER_DISPATCH)->orderBy('id', 'DESC')
            ->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return view('staff.courier.dispatch', compact('pageTitle', 'courierLists'));
    }

    public function upcoming()
    {
        $pageTitle    = 'Upcoming Courier';
        $user         = auth()->user();
        $courierLists = CourierInfo::dateFilter()->searchable(['code'])->where('receiver_branch_id', $user->branch_id)->where('status', Status::COURIER_UPCOMING)->orderBy('id', 'DESC')
            ->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return view('staff.courier.upcoming', compact('pageTitle', 'courierLists'));
    }

    public function dispatched($id)
    {
        $user                = auth()->user();
        $courierInfo         = CourierInfo::where('sender_branch_id', $user->branch_id)->findOrFail($id);
        $courierInfo->status = Status::COURIER_DISPATCH;
        $courierInfo->save();
        $notify[] = ['success', 'Courier dispatched successfully'];
        return back()->withNotify($notify);
    }

    public function deliveryCourier($id)
    {
        $user                = auth()->user();
        $courierInfo         = CourierInfo::where('receiver_branch_id', $user->branch_id)->findOrFail($id);
        $courierInfo->status = Status::COURIER_DELIVERED;
        $courierInfo->save();

        $notify[] = ['success', 'Courier Delivered successfully'];
        return back()->withNotify($notify);
    }

    public function deliveryQueue()
    {
        $pageTitle    = 'Delivery In Queue';
        $courierLists = $this->couriers('deliveryQueue');
        return view('staff.courier.deliveryQueue', compact('pageTitle', 'courierLists'));
    }

    public function delivered()
    {
        $pageTitle    = 'Delivered Courier';
        $courierLists = $this->couriers('delivered');
        return view('staff.courier.list', compact('pageTitle', 'courierLists'));
    }

    protected function couriers($scope = null)
    {
        $user = auth()->user();
        $couriers = CourierInfo::where(function ($query) use ($user) {
            $query->Where('receiver_branch_id', $user->branch_id);
        });
        if ($scope) {
            $couriers = $couriers->$scope();
        }
        $couriers = $couriers->dateFilter()->searchable(['code'])->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return $couriers;
    }

    public function receive($id)
    {
        $courierInfo         = CourierInfo::findOrFail($id);
        $courierInfo->status = Status::COURIER_DELIVERYQUEUE;
        $courierInfo->save();
        $notify[] = ['success', 'Courier received successfully'];
        return back()->withNotify($notify);
    }

    public function courierList()
    {
        $user         = auth()->user();
        $pageTitle    = 'All Courier List';

        $courierLists = CourierInfo::where(function ($query) use ($user) {
            $query->where('sender_branch_id', $user->branch_id)
                ->orWhere('receiver_branch_id', $user->branch_id);
        })
            ->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')
            ->dateFilter()
            ->searchable(['code', 'receiverBranch:name', 'senderBranch:name', 'senderCustomer:mobile', 'receiverCustomer:mobile'])
            ->filter(['status', 'receiver_branch_id', 'sender_branch_id']);
        if (request()->payment_status != null) {
            $courierLists = $courierLists->whereHas('paymentInfo', function ($paymentInfo) {
                $paymentInfo->where('status', request()->payment_status);
            });
        }
        $courierLists = $courierLists->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('staff.courier.list', compact('pageTitle', 'courierLists'));
    }

    public function details($id)
    {
        $pageTitle   = 'Courier Details';
        $courierInfo = CourierInfo::with('products.type.unit', 'senderCustomer', 'receiverCustomer')->findOrFail(decrypt($id));
        $staff = auth()->user();
        return view('staff.courier.details', compact('pageTitle', 'courierInfo', 'staff'));
    }

    public function payment(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);
        $user = auth()->user();

        $courier = CourierInfo::where('code', $request->code)
            ->where(function ($query) use ($user) {
                $query->where('sender_branch_id', $user->branch_id)->orWhere('receiver_branch_id', $user->branch_id);
            })
            ->whereIn('status', [Status::COURIER_QUEUE, Status::COURIER_DELIVERYQUEUE])
            ->firstOrFail();

        $courierPayment = CourierPayment::where('courier_info_id', $courier->id)
            ->where('status', Status::UNPAID)
            ->firstOrFail();

        $courierPayment->receiver_id = $user->id;
        $courierPayment->branch_id   = $user->branch_id;
        $courierPayment->date        = Carbon::now();
        $courierPayment->status      = Status::PAID;
        $courierPayment->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'Courier Payment ' . $user->username;
        $adminNotification->click_url = urlPath('admin.courier.info.details', $courier->id);
        $adminNotification->save();

        $notify[] = ['success', 'Payment completed'];
        return back()->withNotify($notify);
    }

    public function deliveryStore(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:courier_infos,code',
        ]);
        $user = auth()->user();
        $courier = CourierInfo::where('code', $request->code)->where('status', Status::COURIER_DELIVERYQUEUE)->firstOrFail();

        $courier->receiver_staff_id = $user->id;
        $courier->status            = Status::COURIER_DELIVERED;
        $courier->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'Courier Delivery ' . $user->username;
        $adminNotification->click_url = urlPath('admin.courier.info.details', $courier->id);
        $adminNotification->save();

        $notify[] = ['success', 'Delivery completed'];
        return back()->withNotify($notify);
    }

    public function courierAllDispatch(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $ids  = $request->id;
        $id   = explode(',', $ids);
        $user = auth()->user();

        CourierInfo::whereIn('id', $id)->where('sender_branch_id', $user->branch_id)->update(['status' => Status::COURIER_DISPATCH]);
    }

    public function cash()
    {
        $user = auth()->user();
        $pageTitle = 'Courier Cash Collection';
        $branchIncomeLists = CourierPayment::where('receiver_id', $user->id)->select(DB::raw('*,SUM(final_amount) as totalAmount'))->groupBy('date')->paginate(getPaginate());
        return view('staff.courier.cash', compact('pageTitle', 'branchIncomeLists'));
    }

    public function sentCourierList()
    {
        $user = auth()->user();
        $pageTitle = 'Total Dispatch Courier';
        $courierInfo = CourierInfo::dateFilter()->searchable(['code']);
        $courierLists = $courierInfo->where('sender_staff_id', $user->id)->whereIn('status', [Status::COURIER_DISPATCH, Status::COURIER_DELIVERYQUEUE, Status::COURIER_DELIVERED])->orderBy('id', 'DESC')
            ->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')->paginate(getPaginate());
        return view('staff.courier.list', compact('pageTitle', 'courierLists'));
    }

    public function receivedCourierList()
    {
        $user = auth()->user();
        $pageTitle = 'Received Courier List';
        $courierLists = CourierInfo::where('receiver_staff_id', $user->id)->orderBy('id', 'DESC')->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')
            ->paginate(getPaginate());
        return view('staff.courier.list', compact('pageTitle', 'courierLists'));
    }

    public function searchCustomer()
    {
        $customer = Customer::where(request()->searchBy, request()->search)->first();
        return response()->json($customer);
    }

    private function customerInfoUpdate($request, $customerType)
    {
        $customer = new Customer();
        $customer->firstname     = $request->{$customerType . '_customer_firstname'};
        $customer->lastname      = $request->{$customerType . '_customer_lastname'};
        $customer->mobile        = $request->{$customerType . '_customer_phone'};
        $customer->email         = $request->{$customerType . '_customer_email'};
        $customer->address       = $request->{$customerType . '_customer_address'};
        $customer->city          = $request->{$customerType . '_customer_city'};
        $customer->state         = $request->{$customerType . '_customer_state'};
        $customer->save();
        return $customer;
    }

    private function validator($request)
    {
        return $request->validate([
            'branch'           => 'required|exists:branches,id',

            'sender_customer_firstname' => 'required|max:40',
            'sender_customer_lastname'  => 'required|max:40',
            'sender_customer_email'     => 'required|email|max:255',
            'sender_customer_phone'     => 'required|string|max:40',
            'sender_customer_city'      => 'required|max:40',
            'sender_customer_state'     => 'required|max:40',
            'sender_customer_address'   => 'required|max:255',

            'receiver_customer_firstname'   => 'required|max:40',
            'receiver_customer_lastname'    => 'required|max:40',
            'receiver_customer_email'       => 'required|email|max:255',
            'receiver_customer_phone'       => 'required|string|max:40',
            'receiver_customer_address'     => 'required|max:255',
            'receiver_customer_city'        => 'required|max:40',
            'receiver_customer_state'       => 'required|max:40',

            'items'            => 'required|array',
            'items.*.type'     => 'required|integer|exists:types,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.amount'   => 'required|numeric|gt:0',
            'items.*.name'     => 'nullable|string',
            'estimate_date'    => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'payment_status'   => 'required|integer|in:0,1',
        ]);
    }
}
