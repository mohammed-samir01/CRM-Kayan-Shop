<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view dashboard');
    }

    public function __invoke(Request $request)
    {
        $dateRange = $request->input('date_range', 'last_30_days');
        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');

        switch ($dateRange) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'yesterday':
                $startDate = now()->subDay()->startOfDay();
                $endDate = now()->subDay()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = now()->subDays(6)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'last_30_days':
                $startDate = now()->subDays(29)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'last_60_days':
                $startDate = now()->subDays(59)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'last_90_days':
                $startDate = now()->subDays(89)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'this_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            case 'custom':
                $startDate = $customStartDate ? Carbon::parse($customStartDate)->startOfDay() : now()->startOfDay();
                $endDate = $customEndDate ? Carbon::parse($customEndDate)->endOfDay() : now()->endOfDay();
                break;
            default:
                $startDate = now()->subDays(29)->startOfDay();
                $endDate = now()->endOfDay();
                break;
        }

        // Apply Date Filter to Queries
        
        // 1. Leads Count (In Period)
        $leadsCount = Lead::whereBetween('created_at', [$startDate, $endDate])->count();

        // 2. Leads by Status (In Period)
        $leadsByStatus = Lead::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 3. Revenue (In Period)
        $revenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_value');

        // 4. Top Platforms (In Period)
        $topPlatforms = Campaign::selectRaw('platform, COUNT(*) as count')
            ->join('leads', 'campaigns.id', '=', 'leads.campaign_id')
            ->whereBetween('leads.created_at', [$startDate, $endDate])
            ->groupBy('campaigns.platform')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // 5. Recent Leads (In Period) - OR just recent regardless? Usually recent is recent. 
        // Let's keep it recent 10 regardless of filter or filter them?
        // User asked for "Smart Filter", usually applies to stats. 
        // Let's filter recent leads too to match the context.
        $recentLeads = Lead::with(['campaign', 'assignedTo'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(10)
            ->get();

        // 6. Chart Data (Daily Sales/Leads in Period)
        // Group by Date
        $dailySales = Order::selectRaw('DATE(created_at) as date, SUM(total_value) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Fill missing dates
        $chartData = [];
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        
        // Limit chart points if range is too large (e.g. year)? 
        // For now, let's just output daily. If > 90 days, maybe group by week/month?
        // Keeping it simple for now (daily).
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $chartData[$dateStr] = $dailySales[$dateStr] ?? 0;
        }

        // 7. Conversion Rate (In Period)
        $totalLeadsInPeriod = Lead::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalOrdersInPeriod = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $conversionRate = $totalLeadsInPeriod > 0 ? ($totalOrdersInPeriod / $totalLeadsInPeriod) * 100 : 0;

        $ordersCount = $totalOrdersInPeriod;

        $statusTranslations = __('leads.status');
        $platformTranslations = __('campaigns.platform');

        // 8. Top Selling Products (In Period)
        $topProducts = OrderItem::select('product_id', DB::raw('sum(quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id') // Need join to filter by order date
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->with(['product' => fn($q) => $q->withTrashed()])
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 9. Low Stock Alerts (Current State - Not dependent on date range)
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'leadsCount',
            'ordersCount',
            'leadsByStatus',
            'revenue',
            'topPlatforms',
            'recentLeads',
            'chartData',
            'conversionRate',
            'statusTranslations',
            'platformTranslations',
            'topProducts',
            'lowStockProducts',
            'dateRange',
            'startDate',
            'endDate'
        ));
    }
}
