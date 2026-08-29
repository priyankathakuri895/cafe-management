<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'notes'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function totalSpent(): float
    {
        return (float) $this->orders()->paid()->sum('total');
    }

    public function visitCount(): int
    {
        return $this->orders()->paid()->count();
    }
}
