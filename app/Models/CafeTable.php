<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CafeTable extends Model
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_OUT = 'out_of_service';

    protected $fillable = ['name', 'capacity', 'location', 'status'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function openOrder()
    {
        return $this->orders()->where('status', Order::STATUS_OPEN)->latest('id')->first();
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_OUT => 'Out of service',
        ];
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'success',
            self::STATUS_OCCUPIED => 'danger',
            self::STATUS_RESERVED => 'warning',
            default => 'secondary',
        };
    }
}
