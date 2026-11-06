<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $recentUsers = User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']);

        return view('admin.dashboard', compact('totalUsers', 'recentUsers'));
    }

    public function users()
    {
        $users = User::latest()->paginate(20);

        return view('admin.users', compact('users'));
    }
}
