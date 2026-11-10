<?php

namespace App\Http\Controllers;

use App\Models\Register\Group;
use App\Models\Register\History;
use App\Models\Register\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'groups' => Group::count(),
            'histories' => History::count(),
        ];

        $recentHistories = History::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('home.home', compact('stats', 'recentHistories'));
    }
}
