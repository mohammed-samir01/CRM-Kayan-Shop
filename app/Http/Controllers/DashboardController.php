<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $todayLeads = Lead::whereDate('created_at', today())->count();

        $leadsByStatus = Lead::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $revenue7Days = Order::where('created_at', '>=', now()->subDays(7))->sum('total_value');

        $revenue30Days = Order::where('created_at', '>=', now()->subDays(30))->sum('total_value');

        $topPlatforms = Campaign::selectRaw('platform, COUNT(*) as count')
            ->join('leads', 'campaigns.id', '=', 'leads.campaign_id')
            ->groupBy('campaigns.platform')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $recentLeads = Lead::with(['campaign', 'assignedTo'])
            ->latest()
            ->take(10)
            ->get();

        // Daily Sales for the last 30 days
        $dailySales = Order::selectRaw('DATE(created_at) as date, SUM(total_value) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Fill missing dates with 0
        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData[$date] = $dailySales[$date] ?? 0;
        }

        // Conversion Rate
        $totalLeads = Lead::count();
        $totalOrders = Order::count();
        $conversionRate = $totalLeads > 0 ? ($totalOrders / $totalLeads) * 100 : 0;

        $statusTranslations = __('leads.status');
        $platformTranslations = __('campaigns.platform');

        // Top Selling Products
        $topProducts = OrderItem::select('product_id', DB::raw('sum(quantity) as total_sold'))
            ->with(['product' => fn($q) => $q->withTrashed()])
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Low Stock Alerts (Less than 10 items)
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todayLeads',
            'leadsByStatus',
            'revenue7Days',
            'revenue30Days',
            'topPlatforms',
            'recentLeads',
            'chartData',
            'conversionRate',
            'statusTranslations',
            'platformTranslations',
            'topProducts',
            'lowStockProducts'
        ));
    }
}
