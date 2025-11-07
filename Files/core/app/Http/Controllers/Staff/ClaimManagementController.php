<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\SupportService;
use App\Models\SupportClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimManagementController extends Controller
{
    protected SupportService $supportService;

    public function __construct(SupportService $supportService)
    {
        $this->supportService = $supportService;
    }

    /**
     * Show all claims
     */
    public function index()
    {
        $claims = SupportClaim::with(['customer', 'courierInfo', 'supportIssue'])
            ->latest('submitted_at')
            ->paginate(20);

        $pageTitle = 'All Claims';
        return view('staff.claims.index', compact('pageTitle', 'claims'));
    }

    /**
     * Show pending claims
     */
    public function pending()
    {
        $claims = $this->supportService->getPendingClaims();

        // Get SLA warnings
        $nearingSLA = $this->supportService->getClaimsNearingSLA();
        $overdue = $this->supportService->getOverdueClaims();

        $pageTitle = 'Pending Claims';
        return view('staff.claims.pending', compact('pageTitle', 'claims', 'nearingSLA', 'overdue'));
    }

    /**
     * Show claim review form
     */
    public function review(SupportClaim $claim)
    {
        $claim->load(['customer', 'courierInfo', 'supportIssue']);

        // Mark as under review if pending
        if ($claim->status == SupportClaim::STATUS_PENDING) {
            $claim->update(['status' => SupportClaim::STATUS_UNDER_REVIEW]);
        }

        $pageTitle = 'Review Claim';
        return view('staff.claims.review', compact('pageTitle', 'claim'));
    }

    /**
     * Approve claim
     */
    public function approve(Request $request, SupportClaim $claim)
    {
        $validated = $request->validate([
            'approved_amount' => 'required|numeric|min:0',
        ]);

        $staff = Auth::user();

        try {
            // Note: Using admin_id as reviewer since staff might not have permission
            // In production, you might want a separate staff approval table
            $this->supportService->approveClaim($claim, $validated['approved_amount'], 1);

            return redirect()->route('staff.claims.pending')
                ->with('success', "Claim #{$claim->claim_number} approved for " . showAmount($validated['approved_amount']));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve claim: ' . $e->getMessage());
        }
    }

    /**
     * Reject claim
     */
    public function reject(Request $request, SupportClaim $claim)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $staff = Auth::user();

        try {
            $this->supportService->rejectClaim($claim, $validated['rejection_reason'], 1);

            return redirect()->route('staff.claims.pending')
                ->with('success', "Claim #{$claim->claim_number} has been rejected");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject claim: ' . $e->getMessage());
        }
    }

    /**
     * Show claim details
     */
    public function show(SupportClaim $claim)
    {
        $claim->load(['customer', 'courierInfo', 'supportIssue', 'reviewedBy']);

        $pageTitle = 'Claim Details';
        return view('staff.claims.show', compact('pageTitle', 'claim'));
    }
}
