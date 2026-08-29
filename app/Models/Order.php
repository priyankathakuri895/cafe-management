<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_OPEN = 'open';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number', 'cafe_table_id', 'user_id', 'customer_id', 'order_type', 'status',
        'customer_name', 'subtotal', 'discount', 'service_charge', 'tax',
        'total', 'paid_amount', 'payment_method', 'note', 'paid_at', 'stock_deducted',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'stock_deducted' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(CafeTable::class, 'cafe_table_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public static function nextOrderNumber(): string
    {
        $prefix = 'ORD-'.now()->format('Ymd').'-';
        $last = static::where('order_number', 'like', $prefix.'%')
            ->orderByDesc('id')->value('order_number');
        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /** Recalculate money columns from the line items. */
    public function recalculate(?float $discount = null): void
    {
        $subtotal = (float) $this->items()->sum('line_total');
        $discount = $discount ?? (float) $this->discount;
        $discount = min($discount, $subtotal);
        $base = $subtotal - $discount;

        $serviceRate = (float) config('cafe.service_charge_rate', 0);
        $taxRate = (float) config('cafe.tax_rate', 0);

        $service = round($base * $serviceRate / 100, 2);
        $tax = round(($base + $service) * $taxRate / 100, 2);

        $this->forceFill([
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'service_charge' => $service,
            'tax' => $tax,
            'total' => round($base + $service + $tax, 2),
        ])->save();
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'warning',
            self::STATUS_PAID => 'success',
            default => 'secondary',
        };
    }
}
