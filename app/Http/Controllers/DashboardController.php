<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Models\Aduan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = $currentOfMonth->copy()->endOfMonth();

        $totalAduan = Aduan::whereBetween('created_at', [$currentOfMonth, $endOfMonth])->count();
        $activeAduan = Aduan::whereBetween('created_at', [$currentOfMonth, $endOfMonth])
            ->where('status', '!=', 'resolved')
            ->count();
        $resolveAduan = Aduan::whereBetween('created_at', [$currentOfMonth, $endOfMonth])
            ->where('status', '=', 'resolved')
            ->count();
        $avgResolutionTime = Aduan::whereBetween('created_at', [$currentOfMonth, $endOfMonth])
            ->where('status', '=', 'resolved')
            ->whereNotNull('completed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_time'))
            ->value('avg_time') ?? 0;

        $statusDistribution = [
            $open = Aduan::whereBetween('created_at', [$currentOfMonth, $endOfMonth])->where('status', '=', 'open')->count(),
            $onprogress = Aduan::whereBetween('created_at', [$currentOfMonth, $endOfMonth])->where('status', '=', 'onprogress')->count(),
            $resolved = Aduan::whereBetween('created_at', [$currentOfMonth, $endOfMonth])->where('status', '=', 'resolved')->count(),
            $rejected = Aduan::whereBetween('created_at', [$currentOfMonth, $endOfMonth])->where('status', '=', 'rejected')->count(),
        ];

        $dashboradData = [
            'total_aduan' => $totalAduan,
            'active_aduan' => $activeAduan,
            'resolve_aduan' => $resolveAduan,
            'avg_aduan' => $avgResolutionTime,
            'distribusi_aduan' => $statusDistribution
        ];

        return response()->json([
            'message' => 'Dashboard retrieved successfully',
            'data' => DashboardResource::collection($dashboradData)
        ], 200);

    }
}
