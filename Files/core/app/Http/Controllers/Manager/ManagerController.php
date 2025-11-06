<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CourierInfo;
use App\Models\CourierPayment;
use App\Models\SupportMessage;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function dashboard()
    {
        $manager            = auth()->user();
        $pageTitle          = "Manager Dashboard";
        $branchCount        = Branch::active()->count();
        $courierShipCount   = CourierInfo::dispatched()->count();
        $upcomingCount      = CourierInfo::upcoming()->count();
        $courierInfoCount   = $this->couriers()->count();
        $courierQueueCount  = CourierInfo::queue()->count();
        $deliveryQueueCount = CourierInfo::deliveryQueue()->count();
        $totalSentCount     = CourierInfo::where('sender_branch_id', $manager->branch_id)->where('status', '!=', Status::COURIER_QUEUE)->count();

        $courierDelivered = CourierInfo::delivered()->count();
        $totalStaffCount  = User::staff()->where('branch_id', $manager->branch_id)->count();
        $branchIncome     = CourierPayment::where('branch_id', $manager->branch_id)->where('status', Status::PAID)->sum('final_amount');
        $courierInfos     = $this->couriers('queue');

        return view('manager.dashboard', compact('pageTitle', 'branchCount', 'courierShipCount', 'courierQueueCount', 'upcomingCount', 'deliveryQueueCount', 'totalStaffCount', 'totalSentCount', 'branchIncome', 'courierInfoCount', 'courierDelivered', 'courierInfos'));
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

    public function branchList()
    {
        $pageTitle = "Branch list";
        $branches  = Branch::active()->searchable(['name', 'email', 'address'])->orderBy('name')->paginate(getPaginate());
        return view('manager.branch.index', compact('pageTitle', 'branches'));
    }

    public function profile()
    {
        $pageTitle = "Manager Profile";
        $manager   = auth()->user();
        return view('manager.profile', compact('pageTitle', 'manager'));
    }

    public function ticketDelete($id)
    {
        $message = SupportMessage::findOrFail($id);
        $path    = getFilePath('ticket');

        if ($message->attachments()->count() > 0) {

            foreach ($message->attachments as $attachment) {
                fileManager()->removeFile($path . '/' . $attachment->attachment);
                $attachment->delete();
            }
        }

        $message->delete();
        $notify[] = ['success', "Support ticket deleted successfully"];
        return back()->withNotify($notify);
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'firstname' => 'required|max:40',
            'lastname'  => 'required|max:40',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'image'     => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
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
        $notify[] = ['success', 'Your profile added successfully'];
        return redirect()->route('manager.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $user      = auth()->user();
        return view('manager.password', compact('pageTitle', 'user'));
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
        $notify[] = ['success', 'Password changed successfully'];
        return redirect()->route('manager.password')->withNotify($notify);
    }

    public function branchIncome()
    {
        $user          = auth()->user();
        $pageTitle     = "Branch Income";
        $branchIncomes = CourierPayment::where('branch_id', $user->branch_id)
            ->select(DB::raw("*,SUM(final_amount) as totalAmount"))
            ->groupBy('date')->orderby('id', 'DESC')->paginate(getPaginate());
        return view('manager.courier.income', compact('pageTitle', 'branchIncomes'));
    }
}
