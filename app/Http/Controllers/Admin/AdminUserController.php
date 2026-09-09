<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display listing of all users across all roles.
     */
    public function index(Request $request): View
    {
        $role = $request->query('role');
        $status = $request->query('status');
        $search = $request->query('search');

        $query = User::with(['farmerProfile', 'collectorProfile', 'consumerProfile'])
            ->orderBy('created_at', 'desc');

        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        if ($status !== null && $status !== '' && $status !== 'all') {
            $query->where('is_active', $status === '1' || $status === 'active');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        // Counts for tabs
        $counts = [
            'all' => User::count(),
            'petani' => User::where('role', 'petani')->count(),
            'pengepul' => User::where('role', 'pengepul')->count(),
            'konsumen' => User::where('role', 'konsumen')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'role', 'status', 'search', 'counts'));
    }

    /**
     * Display detailed profile of a user.
     */
    public function show(User $user): View
    {
        $user->load([
            'farmerProfile',
            'collectorProfile',
            'consumerProfile',
            'products' => fn($q) => $q->latest()->take(5),
            'buyerOrders' => fn($q) => $q->latest()->take(5),
            'sellerOrders' => fn($q) => $q->latest()->take(5),
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Toggle active/inactive status of a user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Prevent admin from deactivating their own account
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }
}
