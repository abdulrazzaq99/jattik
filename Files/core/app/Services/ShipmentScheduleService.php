<?php

namespace App\Services;

use App\Models\ShipmentSchedule;
use App\Models\CourierInfo;
use Carbon\Carbon;

class ShipmentScheduleService
{
    /**
     * Get all available shipping dates for the next 3 months.
     */
    public function getAvailableShippingDates(): array
    {
        $schedules = ShipmentSchedule::active()->get();
        $availableDates = [];

        foreach ($schedules as $schedule) {
            // Get next 3 occurrences
            for ($i = 0; $i < 3; $i++) {
                $date = $this->getNextOccurrence($schedule, $i);

                if ($schedule->canSelectDate($date)) {
                    $availableDates[] = [
                        'schedule_id' => $schedule->id,
                        'schedule_name' => $schedule->name,
                        'date' => $date,
                        'day_of_month' => $schedule->day_of_month,
                        'cutoff_date' => $date->copy()->subDays($schedule->cutoff_days_before),
                    ];
                }
            }
        }

        // Sort by date
        usort($availableDates, function ($a, $b) {
            return $a['date']->timestamp <=> $b['date']->timestamp;
        });

        return $availableDates;
    }

    /**
     * Get next occurrence of a schedule.
     */
    protected function getNextOccurrence(ShipmentSchedule $schedule, int $monthsAhead = 0): Carbon
    {
        $today = Carbon::today();
        $targetMonth = $today->copy()->addMonths($monthsAhead);

        $date = Carbon::create(
            $targetMonth->year,
            $targetMonth->month,
            min($schedule->day_of_month, $targetMonth->daysInMonth)
        );

        // If the date is in the past or within cutoff, move to next month
        $cutoffDate = $date->copy()->subDays($schedule->cutoff_days_before);

        if ($monthsAhead === 0 && $today->greaterThanOrEqualTo($cutoffDate)) {
            return $this->getNextOccurrence($schedule, 1);
        }

        return $date;
    }

    /**
     * Validate if a shipment date can be selected.
     */
    public function canSelectDate(ShipmentSchedule $schedule, Carbon $date): bool
    {
        $today = Carbon::today();
        $cutoffDate = $date->copy()->subDays($schedule->cutoff_days_before);

        return $today->lessThan($cutoffDate) && $date->greaterThan($today);
    }

    /**
     * Extend shipping date (FR-18).
     */
    public function extendShippingDate(CourierInfo $courier, Carbon $newDate, ShipmentSchedule $newSchedule = null): bool
    {
        // Can only extend if not dispatched yet
        if ($courier->address_locked == 1) {
            return false;
        }

        // Store original date if not already stored
        if (!$courier->original_ship_date) {
            $courier->original_ship_date = $courier->scheduled_ship_date;
        }

        $courier->scheduled_ship_date = $newDate;

        if ($newSchedule) {
            $courier->shipment_schedule_id = $newSchedule->id;
        }

        return $courier->save();
    }

    /**
     * Get upcoming shipments by schedule.
     */
    public function getUpcomingShipments($branchId = null): array
    {
        $query = CourierInfo::query()
            ->whereNotNull('scheduled_ship_date')
            ->where('scheduled_ship_date', '>=', Carbon::today())
            ->where('address_locked', 0)
            ->with(['senderCustomer', 'receiverCustomer', 'senderBranch', 'receiverBranch']);

        if ($branchId) {
            $query->where('sender_branch_id', $branchId);
        }

        return $query->orderBy('scheduled_ship_date')
            ->get()
            ->groupBy(function ($courier) {
                return $courier->scheduled_ship_date->format('Y-m-d');
            })
            ->toArray();
    }
}
