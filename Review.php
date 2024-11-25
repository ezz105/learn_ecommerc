<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_item_id',
        'rating',
        'title',
        'comment',
        'status'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Scopes for analytics
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeWithinPeriod($query, $startDate)
    {
        return $query->where('created_at', '>=', $startDate);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    // Helper methods for analytics
    public static function getAverageRating()
    {
        return self::verified()->avg('rating') ?? 0;
    }

    public static function getRatingDistribution()
    {
        return self::verified()
            ->select('rating', \DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();
    }
}
