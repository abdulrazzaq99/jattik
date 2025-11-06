<?php

namespace App\Http\Controllers\Staff;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CourierInfo;
use App\Models\CourierPayment;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{

    public function dashboard()
    {
        $user            = auth()->user();
        $pageTitle       = "Dashboard";
        $branchCount     = Branch::active()->count();
        $cashCollection  = CourierPayment::where('receiver_id', $user->id)->where('status', Status::PAID)->sum('final_amount');
        $dispatchCourier = CourierInfo::dispatched()->count();
        $sentInQueue     = CourierInfo::queue()->count();
        $deliveryInQueue = CourierInfo::deliveryQueue()->count();
        $upcomingCourier = CourierInfo::upcoming()->count();
        $totalSent       = CourierInfo::where('sender_staff_id', $user->id)->whereIn('status', [Status::COURIER_DISPATCH, Status::COURIER_DELIVERYQUEUE, Status::COURIER_DELIVERED])->count();
        
        $totalDelivery   = CourierInfo::where('receiver_branch_id', $user->branch_id)->where('status', Status::COURIER_DELIVERED)->count();

        $courierDelivery = CourierInfo::upcoming()->orderBy('id', 'DESC')->with('senderBranch', 'receiverBranch', 'senderStaff', 'receiverStaff', 'paymentInfo')->take(5)->get();
        $totalCourier    = CourierInfo::where('sender_branch_id', $user->branch_id)->orWhere('receiver_branch_id', $user->branch_id)->count();
        return view('staff.dashboard', compact('pageTitle', 'branchCount', 'deliveryInQueue', 'totalSent', 'upcomingCourier', 'sentInQueue', 'dispatchCourier', 'cashCollection', 'totalDelivery', 'courierDelivery', 'totalCourier'));
    }

    public function branchList()
    {
        $pageTitle = "Branch List";
        $branches  = Branch::searchable(['name', 'email', 'address'])->active()->orderBy('name', 'ASC')->paginate(getPaginate());
        return view('staff.branch.index', compact('pageTitle', 'branches'));
    }


    public function profile()
    {
        $pageTitle = "Staff Profile";
        $staff     = auth()->user();
        return view('staff.profile', compact('pageTitle', 'staff'));
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'firstname' => 'required|max:40',
            'lastname'  => 'required|max:40',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'image'     => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image ?: null;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->email     = $request->email;
        $user->save();
        $notify[] = ['success', 'Your profile updated successfully.'];
        return redirect()->route('staff.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $user      = auth()->user();
        return view('staff.password', compact('pageTitle', 'user'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password'     => 'required|min:5|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password do not match !!'];
            return back()->withNotify($notify);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return redirect()->route('staff.password')->withNotify($notify);
    }
}
