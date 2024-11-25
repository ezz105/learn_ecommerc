<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'stock',
        'status',
        'sales_count',
        'view_count',
        'average_rating',
        'reviews_count'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'sales_count' => 'integer',
        'view_count' => 'integer',
        'reviews_count' => 'integer',
        'stock' => 'integer'
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Stock Management
    public function hasStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    public function decrementStock(int $quantity = 1): bool
    {
        if (!$this->hasStock($quantity)) {
            return false;
        }
        
        return $this->decrement('stock', $quantity);
    }

    public function incrementStock(int $quantity = 1): bool
    {
        return $this->increment('stock', $quantity);
    }

    public function updateStock(int $newQuantity): bool
    {
        return $this->update(['stock' => $newQuantity]);
    }

    // Analytics Scopes
    public function scopeTopSelling($query, $limit = 10)
    {
        return $query->orderBy('sales_count', 'desc')
                    ->take($limit);
    }

    public function scopeTopRated($query, $limit = 10)
    {
        return $query->where('reviews_count', '>', 0)
                    ->orderBy('average_rating', 'desc')
                    ->orderBy('reviews_count', 'desc')
                    ->take($limit);
    }

    public function scopeMostViewed($query, $limit = 10)
    {
        return $query->orderBy('view_count', 'desc')
                    ->take($limit);
    }

    public function scopeLowStock($query, $threshold = 5)
    {
        return $query->where('stock', '<=', $threshold)
                    ->where('stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', 0);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Helper Methods
    public function incrementSales(int $quantity = 1): bool
    {
        return $this->increment('sales_count', $quantity);
    }

    public function incrementViews(): bool
    {
        return $this->increment('view_count');
    }

    public function updateAverageRating(): bool
    {
        $averageRating = $this->reviews()
            ->where('status', 'approved')
            ->avg('rating') ?? 0;
            
        $reviewsCount = $this->reviews()
            ->where('status', 'approved')
            ->count();

        return $this->update([
            'average_rating' => round($averageRating, 2),
            'reviews_count' => $reviewsCount
        ]);
    }

    // Analytics Methods
    public static function getTopPerformers($limit = 10)
    {
        return static::with(['category'])
            ->select([
                'products.*',
                \DB::raw('(sales_count * price) as revenue')
            ])
            ->orderBy('revenue', 'desc')
            ->take($limit)
            ->get();
    }

    public static function getCategoryPerformance()
    {
        return static::join('categories', 'products.category_id', '=', 'categories.id')
            ->select([
                'categories.name',
                \DB::raw('COUNT(products.id) as products_count'),
                \DB::raw('SUM(products.sales_count) as total_sales')
            ])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->get();
    }

    public static function getInventoryStatus()
    {
        return [
            'total_products' => static::count(),
            'out_of_stock' => static::outOfStock()->count(),
            'low_stock' => static::lowStock()->count(),
            'in_stock' => static::where('stock', '>', 5)->count()
        ];
    }
}
