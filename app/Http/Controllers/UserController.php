<?php

namespace App\Http\Controllers;

use App\Models\Register\Group;
use App\Models\Register\User;
use App\Services\HistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly HistoryService $historyService
    ) {}

    public function index(Request $request): View
    {
        $query = User::with('group')->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('register.users.index-users', compact('users'));
    }

    public function show(Request $request, ?User $user = null): View
    {
        $user = ($user ?? $request->user())->load('group');

        return view('account.index-account', compact('user'));
    }

    public function create(): View
    {
        $groups = Group::orderBy('description')->get();

        return view('register.users.form-users', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'group_id' => ['required', 'exists:groups,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'group_id' => $validated['group_id'],
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->historyService->recordCreated($user);

        return redirect()
            ->route('register.users.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function edit(User $user): View
    {
        $user->load('auditHistories.user');
        $groups = Group::orderBy('description')->get();
        $histories = $user->auditHistories;

        return view('register.users.form-users', compact('user', 'groups', 'histories'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'group_id' => ['required', 'exists:groups,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'group_id' => $validated['group_id'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        $this->historyService->recordUpdated($user);

        return redirect()
            ->route('register.users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->historyService->recordDeleted($user);
        $user->delete();

        return redirect()
            ->route('register.users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
