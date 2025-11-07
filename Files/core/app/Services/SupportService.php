<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CourierInfo;
use App\Models\SupportIssue;
use App\Models\SupportClaim;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupportService
{
    /**
     * Create a new support issue (FR-31)
     */
    public function createIssue(Customer $customer, array $data): SupportIssue
    {
        DB::beginTransaction();

        try {
            // Handle file uploads if any
            $attachments = [];
            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $path = $file->store('support/issues', 'public');
                    $attachments[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ];
                }
            }

            $issue = SupportIssue::create([
                'customer_id' => $customer->id,
                'courier_info_id' => $data['courier_info_id'] ?? null,
                'issue_type' => $data['issue_type'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? SupportIssue::PRIORITY_MEDIUM,
                'attachments' => $attachments,
            ]);

            // Notify admin of new issue
            $admins = \App\Models\Admin::where('status', 1)->get();
            foreach ($admins as $admin) {
                notify($admin, 'NEW_SUPPORT_ISSUE', [
                    'issue_number' => $issue->issue_number,
                    'customer_name' => $customer->fullname,
                    'issue_type' => $issue->issue_type,
                    'subject' => $issue->subject,
                ]);
            }

            DB::commit();
            return $issue;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Assign issue to staff member
     */
    public function assignIssue(SupportIssue $issue, int $staffId): bool
    {
        $staff = User::findOrFail($staffId);

        $issue->assignTo($staffId);

        // Notify staff member
        notify($staff, 'ISSUE_ASSIGNED', [
            'issue_number' => $issue->issue_number,
            'customer_name' => $issue->customer->fullname,
            'issue_type' => $issue->issue_type,
        ]);

        return true;
    }

    /**
     * Resolve issue
     */
    public function resolveIssue(SupportIssue $issue, string $resolution = null): bool
    {
        $issue->markAsResolved();

        // Notify customer
        notify($issue->customer, 'ISSUE_RESOLVED', [
            'issue_number' => $issue->issue_number,
            'resolution' => $resolution ?? 'Your issue has been resolved.',
        ]);

        return true;
    }

    /**
     * Create a claim from an issue (FR-32)
     */
    public function createClaim(Customer $customer, array $data): SupportClaim
    {
        DB::beginTransaction();

        try {
            // Handle evidence files
            $evidence = [];
            if (isset($data['evidence'])) {
                foreach ($data['evidence'] as $file) {
                    $path = $file->store('support/claims', 'public');
                    $evidence[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            $claim = SupportClaim::create([
                'customer_id' => $customer->id,
                'courier_info_id' => $data['courier_info_id'],
                'support_issue_id' => $data['support_issue_id'] ?? null,
                'claim_type' => $data['claim_type'],
                'claimed_amount' => $data['claimed_amount'],
                'claim_details' => $data['claim_details'],
                'evidence' => $evidence,
            ]);

            // Notify admins
            $admins = \App\Models\Admin::where('status', 1)->get();
            foreach ($admins as $admin) {
                notify($admin, 'NEW_CLAIM_SUBMITTED', [
                    'claim_number' => $claim->claim_number,
                    'customer_name' => $customer->fullname,
                    'claim_amount' => showAmount($claim->claimed_amount),
                    'claim_type' => $claim->claim_type,
                ]);
            }

            // Notify customer
            notify($customer, 'CLAIM_SUBMITTED', [
                'claim_number' => $claim->claim_number,
                'claim_amount' => showAmount($claim->claimed_amount),
                'sla_days' => SupportClaim::SLA_DAYS,
            ]);

            DB::commit();
            return $claim;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Approve a claim
     */
    public function approveClaim(SupportClaim $claim, float $approvedAmount, int $reviewerId): bool
    {
        $claim->approve($approvedAmount, $reviewerId);

        // Notify customer
        notify($claim->customer, 'CLAIM_APPROVED', [
            'claim_number' => $claim->claim_number,
            'approved_amount' => showAmount($approvedAmount),
        ]);

        return true;
    }

    /**
     * Reject a claim
     */
    public function rejectClaim(SupportClaim $claim, string $reason, int $reviewerId): bool
    {
        $claim->reject($reason, $reviewerId);

        // Notify customer
        notify($claim->customer, 'CLAIM_REJECTED', [
            'claim_number' => $claim->claim_number,
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Mark claim as paid
     */
    public function markClaimAsPaid(SupportClaim $claim): bool
    {
        $claim->markAsPaid();

        // Notify customer
        notify($claim->customer, 'CLAIM_PAID', [
            'claim_number' => $claim->claim_number,
            'paid_amount' => showAmount($claim->approved_amount),
        ]);

        return true;
    }

    /**
     * Get claims nearing SLA deadline
     */
    public function getClaimsNearingSLA()
    {
        return SupportClaim::nearingSLA()
            ->with(['customer', 'courierInfo'])
            ->get();
    }

    /**
     * Get overdue claims
     */
    public function getOverdueClaims()
    {
        return SupportClaim::overdueSLA()
            ->with(['customer', 'courierInfo'])
            ->get();
    }

    /**
     * Check SLA for all claims (called by cron)
     */
    public function checkClaimsSLA(): array
    {
        $results = [
            'nearing_deadline' => 0,
            'overdue' => 0,
        ];

        // Get claims nearing deadline (2 days left)
        $nearingClaims = $this->getClaimsNearingSLA();
        foreach ($nearingClaims as $claim) {
            // Notify admin
            $admins = \App\Models\Admin::where('status', 1)->get();
            foreach ($admins as $admin) {
                notify($admin, 'CLAIM_SLA_WARNING', [
                    'claim_number' => $claim->claim_number,
                    'days_remaining' => $claim->days_until_deadline,
                    'customer_name' => $claim->customer->fullname,
                ]);
            }
            $results['nearing_deadline']++;
        }

        // Get overdue claims
        $overdueClaims = $this->getOverdueClaims();
        foreach ($overdueClaims as $claim) {
            // Notify admin with high priority
            $admins = \App\Models\Admin::where('status', 1)->get();
            foreach ($admins as $admin) {
                notify($admin, 'CLAIM_SLA_OVERDUE', [
                    'claim_number' => $claim->claim_number,
                    'customer_name' => $claim->customer->fullname,
                    'days_overdue' => abs($claim->days_until_deadline),
                ]);
            }
            $results['overdue']++;
        }

        return $results;
    }

    /**
     * Get customer's issues
     */
    public function getCustomerIssues(Customer $customer, int $perPage = 20)
    {
        return SupportIssue::where('customer_id', $customer->id)
            ->with(['courierInfo', 'assignedTo'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get customer's claims
     */
    public function getCustomerClaims(Customer $customer, int $perPage = 20)
    {
        return SupportClaim::where('customer_id', $customer->id)
            ->with(['courierInfo', 'supportIssue'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get open issues for staff dashboard
     */
    public function getOpenIssues(int $staffId = null)
    {
        $query = SupportIssue::open()
            ->with(['customer', 'courierInfo']);

        if ($staffId) {
            $query->where('assigned_to', $staffId);
        }

        return $query->latest()->get();
    }

    /**
     * Get pending claims for admin review
     */
    public function getPendingClaims()
    {
        return SupportClaim::pending()
            ->with(['customer', 'courierInfo', 'supportIssue'])
            ->orderBy('submitted_at')
            ->get();
    }
}
