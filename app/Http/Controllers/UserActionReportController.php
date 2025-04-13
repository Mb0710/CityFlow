<?php

namespace App\Http\Controllers;

use App\Models\UserAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserActionReportController extends Controller
{
    public function showReport()
    {
        $report = $this->generateUserActionReport();

        return view('user_actions', compact('report'));
    }

    public function showFilteredReport(Request $request)
    {
        $query = UserAction::query();

        if ($request->has('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $actions = $query->with(['user', 'connectedObject'])->get();

        $actionTypes = UserAction::distinct('action_type')->pluck('action_type');
        $users = User::all();

        return view('reports.filtered_user_actions', compact('actions', 'actionTypes', 'users'));
    }

    private function generateUserActionReport()
    {
        // Période d'analyse - 30 derniers jours par défaut
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Total des actions
        $totalActions = UserAction::count();

        // Actions par type
        $actionsByType = UserAction::select('action_type', DB::raw('count(*) as count'))
            ->groupBy('action_type')
            ->orderBy('count', 'desc')
            ->get();

        // Points totaux distribués
        $totalPoints = UserAction::sum('points');

        // Actions par jour (30 derniers jours)
        $actionsByDay = UserAction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Utilisateurs les plus actifs
        $mostActiveUsers = UserAction::select('user_id', DB::raw('count(*) as action_count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('action_count', 'desc')
            ->limit(10)
            ->get();

        // Objets les plus interagis
        $mostInteractedObjects = UserAction::select('object_id', DB::raw('count(*) as interaction_count'))
            ->with('connectedObject')
            ->groupBy('object_id')
            ->orderBy('interaction_count', 'desc')
            ->limit(10)
            ->get();

        // Taux de connexion utilisateurs (basé sur les logins dans les 30 derniers jours)
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('last_login_date')
            ->where('last_login_date', '>=', $startDate)
            ->count();
        $connectionRate = $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;

        // Distribution des utilisateurs par niveau d'activité
        $userActivityLevels = [
            'très actifs' => 0,    // > 20 actions
            'actifs' => 0,         // 6-20 actions
            'peu actifs' => 0,     // 1-5 actions
            'inactifs' => 0        // 0 action
        ];

        $userActionCounts = UserAction::select('user_id', DB::raw('count(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->get()
            ->pluck('count', 'user_id')
            ->toArray();

        foreach (User::pluck('id') as $userId) {
            $actionCount = $userActionCounts[$userId] ?? 0;

            if ($actionCount > 20) {
                $userActivityLevels['très actifs']++;
            } elseif ($actionCount >= 6) {
                $userActivityLevels['actifs']++;
            } elseif ($actionCount >= 1) {
                $userActivityLevels['peu actifs']++;
            } else {
                $userActivityLevels['inactifs']++;
            }
        }

        // Date de la dernière action
        $lastActionDate = UserAction::max('created_at');

        // Points moyens par action
        $averagePointsPerAction = $totalActions > 0 ? $totalPoints / $totalActions : 0;

        // Tendance d'activité (comparaison avec le mois précédent)
        $previousMonth = [
            'start' => Carbon::now()->subDays(60),
            'end' => Carbon::now()->subDays(31)
        ];

        $currentMonthActions = UserAction::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousMonthActions = UserAction::whereBetween('created_at', [$previousMonth['start'], $previousMonth['end']])->count();

        $activityTrend = $previousMonthActions > 0
            ? (($currentMonthActions - $previousMonthActions) / $previousMonthActions) * 100
            : 0;

        return [
            'total_actions' => $totalActions,
            'actions_by_type' => $actionsByType,
            'total_points' => $totalPoints,
            'actions_by_day' => $actionsByDay,
            'most_active_users' => $mostActiveUsers,
            'most_interacted_objects' => $mostInteractedObjects,
            'connection_rate' => $connectionRate,
            'active_users' => $activeUsers,
            'total_users' => $totalUsers,
            'user_activity_levels' => $userActivityLevels,
            'last_action_date' => $lastActionDate,
            'average_points_per_action' => $averagePointsPerAction,
            'activity_trend' => $activityTrend,
            'current_month_actions' => $currentMonthActions,
            'previous_month_actions' => $previousMonthActions
        ];
    }
}