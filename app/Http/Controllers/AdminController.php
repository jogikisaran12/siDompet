<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalRegularUsers = User::where('role', 'user')->count();
        $recentUsers = User::latest()->take(8)->get(['id', 'name', 'email', 'role', 'created_at']);

        return view('admin.dashboard', compact('totalUsers', 'totalAdmins', 'totalRegularUsers', 'recentUsers'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->role, ['admin', 'user'], true)) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function showUser(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $auth = Auth::user();

        if ($auth->id === $user->id && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Anda tidak dapat menurunkan role akun Anda sendiri.']);
        }

        if ($user->role === 'admin' && $data['role'] === 'user' && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['role' => 'Tidak boleh menurunkan Admin terakhir.']);
        }

        $user->update(['role' => $data['role']]);

        return back()->with('success', 'Role pengguna berhasil diperbarui.');
    }

    public function admins()
    {
        $admins = User::where('role', 'admin')->latest()->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    public function createAdmin()
    {
        return view('admin.admins.create');
    }

    public function storeAdmin(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        return redirect()->route('admin.admins')->with('success', 'Akun Admin berhasil dibuat.');
    }

    public function editAdmin(User $user)
    {
        abort_unless($user->role === 'admin', 404);

        return view('admin.admins.edit', compact('user'));
    }

    public function updateAdmin(Request $request, User $user)
    {
        abort_unless($user->role === 'admin', 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return back()->with('success', 'Profil Admin berhasil diperbarui.');
    }

    public function updateAdminPassword(Request $request, User $user)
    {
        abort_unless($user->role === 'admin', 404);

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password Admin berhasil diperbarui.');
    }
}
