<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ShipmentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'day_of_month',
        'description',
        'is_active',
        'cutoff_days_before',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_month' => 'integer',
        'cutoff_days_before' => 'integer',
    ];

    /**
     * Get courier infos for this schedule.
     */
    public function courierInfos(): HasMany
    {
        return $this->hasMany(CourierInfo::class);
    }

    /**
     * Scope to get active schedules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get next shipping date based on this schedule.
     */
    public function getNextShippingDate(): Carbon
    {
        $today = Carbon::today();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        // Try current month first
        $nextDate = Carbon::create($currentYear, $currentMonth, min($this->day_of_month, Carbon::create($currentYear, $currentMonth)->daysInMonth));

        // If the date has passed or is within cutoff period, move to next month
        $cutoffDate = $nextDate->copy()->subDays($this->cutoff_days_before);

        if ($today->greaterThanOrEqualTo($cutoffDate)) {
            $nextDate->addMonth();
            $nextDate->day = min($this->day_of_month, $nextDate->daysInMonth);
        }

        return $nextDate;
    }

    /**
     * Check if a date can be selected (not past cutoff).
     */
    public function canSelectDate(Carbon $date): bool
    {
        $cutoffDate = $date->copy()->subDays($this->cutoff_days_before);
        return Carbon::today()->lessThan($cutoffDate);
    }
}
