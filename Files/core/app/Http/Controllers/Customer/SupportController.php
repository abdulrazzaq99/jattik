<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\SupportService;
use App\Models\CourierInfo;
use App\Models\SupportIssue;
use App\Models\SupportClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    protected SupportService $supportService;

    public function __construct(SupportService $supportService)
    {
        $this->supportService = $supportService;
    }

    /**
     * Display customer's issues
     */
    public function issues()
    {
        $customer = Auth::guard('customer')->user();
        $issues = $this->supportService->getCustomerIssues($customer);

        // Calculate statistics
        $allIssues = SupportIssue::where('customer_id', $customer->id)->get();
        $openCount = $allIssues->where('status', 0)->count();
        $inProgressCount = $allIssues->where('status', 1)->count();
        $resolvedCount = $allIssues->where('status', 2)->count();
        $totalIssues = $allIssues->count();

        $pageTitle = 'My Issues';
        return view('customer.support.issues', compact('pageTitle', 'issues', 'openCount', 'inProgressCount', 'resolvedCount', 'totalIssues'));
    }

    /**
     * Show create issue form
     */
    public function createIssue()
    {
        $customer = Auth::guard('customer')->user();

        // Get customer's shipments for issue reporting
        $recentCouriers = CourierInfo::where(function($query) use ($customer) {
            $query->where('sender_customer_id', $customer->id)
                  ->orWhere('receiver_customer_id', $customer->id);
        })->latest()->limit(20)->get();

        $pageTitle = 'Report an Issue';
        return view('customer.support.create_issue', compact('pageTitle', 'recentCouriers'));
    }

    /**
     * Store new issue
     */
    public function storeIssue(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'courier_info_id' => 'nullable|exists:courier_infos,id',
            'issue_type' => 'required|in:wrong_parcel,damaged,missing,delay,other',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        try {
            $issue = $this->supportService->createIssue($customer, $validated);

            return redirect()->route('customer.support.issues')
                ->with('success', "Issue #{$issue->issue_number} has been submitted. Our team will review it shortly.");
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to submit issue: ' . $e->getMessage());
        }
    }

    /**
     * Show issue details
     */
    public function showIssue(SupportIssue $issue)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure customer owns this issue
        if ($issue->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        $issue->load(['courierInfo', 'assignedTo', 'claim']);

        $pageTitle = 'Issue Details';
        return view('customer.support.show_issue', compact('pageTitle', 'issue'));
    }

    /**
     * Display customer's claims
     */
    public function claims()
    {
        $customer = Auth::guard('customer')->user();
        $claims = $this->supportService->getCustomerClaims($customer);

        // Calculate statistics
        $allClaims = SupportClaim::where('customer_id', $customer->id)->get();
        $pendingCount = $allClaims->whereIn('status', [0, 1])->count(); // Pending + Under Review
        $approvedCount = $allClaims->whereIn('status', [2, 4])->count(); // Approved + Paid
        $rejectedCount = $allClaims->where('status', 3)->count();
        $totalApproved = $allClaims->whereIn('status', [2, 4])->sum('approved_amount');

        $pageTitle = 'My Claims';
        return view('customer.support.claims', compact('pageTitle', 'claims', 'pendingCount', 'approvedCount', 'rejectedCount', 'totalApproved'));
    }

    /**
     * Show create claim form
     */
    public function createClaim()
    {
        $customer = Auth::guard('customer')->user();

        // Get customer's shipments
        $recentCouriers = CourierInfo::where(function($query) use ($customer) {
            $query->where('sender_customer_id', $customer->id)
                  ->orWhere('receiver_customer_id', $customer->id);
        })->latest()->limit(20)->get();

        // Get customer's issues
        $recentIssues = SupportIssue::where('customer_id', $customer->id)
            ->whereNull('claim_id')
            ->latest()
            ->get();

        $pageTitle = 'Submit a Claim';
        return view('customer.support.create_claim', compact('pageTitle', 'recentCouriers', 'recentIssues'));
    }

    /**
     * Store new claim
     */
    public function storeClaim(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'courier_info_id' => 'required|exists:courier_infos,id',
            'support_issue_id' => 'nullable|exists:support_issues,id',
            'claim_type' => 'required|in:damage,loss,delay_compensation',
            'claimed_amount' => 'required|numeric|min:0',
            'claim_details' => 'required|string',
            'evidence.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ]);

        try {
            $claim = $this->supportService->createClaim($customer, $validated);

            return redirect()->route('customer.support.claims')
                ->with('success', "Claim #{$claim->claim_number} has been submitted. It will be processed within " . SupportClaim::SLA_DAYS . " business days.");
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to submit claim: ' . $e->getMessage());
        }
    }

    /**
     * Show claim details
     */
    public function showClaim(SupportClaim $claim)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure customer owns this claim
        if ($claim->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        $claim->load(['courierInfo', 'supportIssue', 'reviewedBy']);

        $pageTitle = 'Claim Details';
        return view('customer.support.show_claim', compact('pageTitle', 'claim'));
    }
}
