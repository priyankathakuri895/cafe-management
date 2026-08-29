<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'cafe_table_id', 'customer_name', 'phone', 'guests',
        'reserved_at', 'status', 'notes', 'created_by',
    ];

    protected $casts = ['reserved_at' => 'datetime'];

    public function table(): BelongsTo
    {
        return $this->belongsTo(CafeTable::class, 'cafe_table_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function statuses(): array
    {
        return [
            'booked' => 'Booked',
            'seated' => 'Seated',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No show',
        ];
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'booked' => 'warning',
            'seated' => 'primary',
            'completed' => 'success',
            'cancelled', 'no_show' => 'secondary',
            default => 'light',
        };
    }
}
