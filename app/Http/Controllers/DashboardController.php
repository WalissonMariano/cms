<?php

namespace App\Http\Controllers;

use App\Models\Register\Group;
use App\Models\Register\History;
use App\Models\Register\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'groups' => Group::count(),
            'histories' => History::count(),
        ];

        $actionLabels = [
            History::ACTION_CREATED => 'Cadastrados',
            History::ACTION_UPDATED => 'Atualizados',
            History::ACTION_DELETED => 'Excluídos',
        ];

        $actionTotals = History::query()
            ->selectRaw('action, count(*) as total')
            ->groupBy('action')
            ->pluck('total', 'action');

        $actionCounts = collect($actionLabels)->map(
            fn (string $label, string $action) => (int) ($actionTotals[$action] ?? 0)
        );

        $actionMax = max(1, $actionCounts->max());
        $actionBars = $actionCounts->map(
            fn (int $count) => (int) round($count / $actionMax * 100)
        );

        $groups = Group::withCount('users')->orderBy('description')->get();

        $weekStart = now()->startOfDay()->subDays(6);
        $dayLabels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $historyByDay = History::query()
            ->where('created_at', '>=', $weekStart)
            ->get()
            ->countBy(fn (History $history) => $history->created_at?->toDateString());

        $weeklyActivity = collect(range(0, 6))->map(function (int $offset) use ($weekStart, $dayLabels, $historyByDay) {
            $date = $weekStart->copy()->addDays($offset);

            return [
                'label' => $dayLabels[$date->dayOfWeek],
                'count' => (int) ($historyByDay[$date->toDateString()] ?? 0),
            ];
        });

        $weekMax = max(1, $weeklyActivity->max('count'));
        $weeklyActivity = $weeklyActivity->map(function (array $day) use ($weekMax) {
            $day['height'] = (int) round($day['count'] / $weekMax * 100);

            return $day;
        });

        return view('dashboard.dashboard', compact(
            'stats',
            'actionLabels',
            'actionCounts',
            'actionBars',
            'groups',
            'weeklyActivity',
        ));
    }
}
