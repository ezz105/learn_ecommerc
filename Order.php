<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'address_id',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_method',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes for analytics
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeWithinPeriod($query, $startDate)
    {
        return $query->where('created_at', '>=', $startDate);
    }

    // Helper methods for analytics
    public static function getTotalRevenue()
    {
        return self::completed()->sum('total_amount');
    }

    public static function getAverageOrderValue()
    {
        return self::completed()->avg('total_amount');
    }
}
