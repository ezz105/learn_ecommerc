<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Get sales analytics data
     */
    public function getSalesAnalytics(Request $request)
    {
        $timeFrame = $request->input('timeFrame', 'month');
        $startDate = $this->getStartDate($timeFrame);

        $salesData = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('AVG(total_amount) as average_order_value')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'total_sales' => $salesData->sum('total_sales'),
            'total_orders' => $salesData->sum('orders_count'),
            'average_order_value' => $salesData->avg('average_order_value')
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'sales_trend' => $salesData,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get order analytics data
     */
    public function getOrderAnalytics()
    {
        $ordersByStatus = Order::select('status', 
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total_amount')
        )
            ->groupBy('status')
            ->get();

        $recentOrders = Order::with(['user', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'orders_by_status' => $ordersByStatus,
                'recent_orders' => $recentOrders
            ]
        ]);
    }

    /**
     * Get product analytics data
     */
    public function getProductAnalytics()
    {
        $topProducts = Product::withCount(['orderItems', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();

        $categoryPerformance = Product::getCategoryPerformance();

        return response()->json([
            'status' => 'success',
            'data' => [
                'top_products' => $topProducts,
                'category_performance' => $categoryPerformance
            ]
        ]);
    }

    /**
     * Get review analytics data
     */
    public function getReviewAnalytics()
    {
        $ratingDistribution = Review::select('rating', DB::raw('COUNT(*) as count'))
            ->where('status', 'approved')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        $recentReviews = Review::with(['user', 'product'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $averageRating = Review::where('status', 'approved')->avg('rating') ?? 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'rating_distribution' => $ratingDistribution,
                'recent_reviews' => $recentReviews,
                'average_rating' => round($averageRating, 2)
            ]
        ]);
    }

    /**
     * Get dashboard overview data
     */
    public function getDashboardOverview()
    {
        // Get today's and this month's sales
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $dailySales = Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $monthlySales = Order::where('status', 'completed')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total_amount');

        // Get pending orders count
        $pendingOrders = Order::where('status', 'pending')->count();

        // Get product and review counts
        $totalProducts = Product::count();
        $totalReviews = Review::where('status', 'approved')->count();

        // Get inventory status
        $inventoryStatus = Product::getInventoryStatus();

        return response()->json([
            'status' => 'success',
            'data' => [
                'daily_sales' => $dailySales,
                'monthly_sales' => $monthlySales,
                'pending_orders' => $pendingOrders,
                'total_products' => $totalProducts,
                'total_reviews' => $totalReviews,
                'inventory_status' => $inventoryStatus
            ]
        ]);
    }

    /**
     * Helper method to get start date based on time frame
     */
    private function getStartDate($timeFrame)
    {
        return match($timeFrame) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };
    }
}
