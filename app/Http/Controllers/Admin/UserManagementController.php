<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserNotification;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{


    public function index(Request $request)
    {
        $query = User::with(['roles', 'activeSubscription.subscriptionPlan']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('subscription_status')) {
            if ($request->subscription_status === 'active') {
                $query->whereHas('activeSubscription');
            } elseif ($request->subscription_status === 'inactive') {
                $query->whereDoesntHave('activeSubscription');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $user->load([
            'roles',
            'subscriptions.subscriptionPlan',
            'payments',
            'userBots.tradingBot',
            'userNotifications' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        $stats = [
            'total_payments' => $user->payments()->count(),
            'total_spent' => $user->payments()->where('payment_status', 'finished')->sum('price_amount'),
            'active_bots' => $user->userBots()->active()->count(),
            'total_notifications' => $user->userNotifications()->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'email_verified' => 'boolean',
            'api_enabled' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => $request->has('email_verified') ? now() : null,
            'api_enabled' => $request->has('api_enabled'),
        ]);

        $user->assignRole($validated['roles']);

        // Create notification for the new user
        UserNotification::createForUser(
            $user,
            'account',
            'Welcome to Gemini Pro Trader',
            'Your account has been created by an administrator. You can now log in and start using our services.',
            ['created_by_admin' => true],
            'high'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the current admin user
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Cancel active subscriptions
        $user->subscriptions()->active()->update(['status' => 'cancelled']);

        // Deactivate user bots
        $user->userBots()->active()->update(['status' => 'inactive']);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function suspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot suspend your own account.');
        }

        $user->update(['suspended_at' => now()]);

        // Cancel active subscriptions
        $user->subscriptions()->active()->update(['status' => 'suspended']);

        // Deactivate user bots
        $user->userBots()->active()->update(['status' => 'suspended']);

        // Create notification
        UserNotification::createForUser(
            $user,
            'account',
            'Account Suspended',
            'Your account has been suspended by an administrator. Please contact support for more information.',
            ['suspended_by' => auth()->id()],
            'high'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User suspended successfully.');
    }

    public function unsuspend(User $user)
    {
        $user->update(['suspended_at' => null]);

        // Reactivate subscriptions that were suspended
        $user->subscriptions()->where('status', 'suspended')->update(['status' => 'active']);

        // Reactivate user bots that were suspended
        $user->userBots()->where('status', 'suspended')->update(['status' => 'active']);

        // Create notification
        UserNotification::createForUser(
            $user,
            'account',
            'Account Reactivated',
            'Your account has been reactivated. You can now access all services again.',
            ['unsuspended_by' => auth()->id()],
            'high'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User unsuspended successfully.');
    }

    public function impersonate(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot impersonate yourself.');
        }

        session(['impersonating' => auth()->id()]);
        auth()->login($user);

        return redirect()
            ->route('dashboard')
            ->with('info', 'You are now impersonating ' . $user->name);
    }

    public function stopImpersonating()
    {
        if (!session('impersonating')) {
            return redirect()->route('dashboard');
        }

        $adminId = session('impersonating');
        session()->forget('impersonating');
        
        $admin = User::find($adminId);
        auth()->login($admin);

        return redirect()
            ->route('admin.dashboard')
            ->with('info', 'Stopped impersonating user.');
    }

    public function sendNotification(Request $request, User $user)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'action_url' => 'nullable|url',
        ]);

        UserNotification::createForUser(
            $user,
            'admin',
            $validated['title'],
            $validated['message'],
            ['sent_by_admin' => auth()->id()],
            $validated['priority'],
            $validated['action_url']
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Notification sent successfully.');
    }

    public function verifyEmail(User $user)
    {
        if ($user->email_verified_at) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('info', 'Email is already verified.');
        }

        $user->update(['email_verified_at' => now()]);

        // Create notification
        UserNotification::createForUser(
            $user,
            'account',
            'Email Verified',
            'Your email address has been verified by an administrator.',
            ['verified_by_admin' => auth()->id()],
            'medium'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Email verified successfully.');
    }

    public function generateApiKey(User $user)
    {
        if ($user->api_key) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('info', 'User already has an API key.');
        }

        $apiKey = 'gpt_' . Str::random(40);
        $user->update([
            'api_key' => $apiKey,
            'api_enabled' => true
        ]);

        // Create notification
        UserNotification::createForUser(
            $user,
            'api',
            'API Key Generated',
            'Your API key has been generated. You can now access the API endpoints.',
            ['generated_by_admin' => auth()->id()],
            'medium'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'API key generated successfully.');
    }

    public function regenerateApiKey(User $user)
    {
        $apiKey = 'gpt_' . Str::random(40);
        $user->update(['api_key' => $apiKey]);

        // Create notification
        UserNotification::createForUser(
            $user,
            'api',
            'API Key Regenerated',
            'Your API key has been regenerated. Please update your applications with the new key.',
            ['regenerated_by_admin' => auth()->id()],
            'high'
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'API key regenerated successfully.');
    }

    public function resetPassword(User $user)
    {
        $newPassword = Str::random(12);
        $user->update(['password' => Hash::make($newPassword)]);

        // Create notification
        UserNotification::createForUser(
            $user,
            'security',
            'Password Reset',
            "Your password has been reset by an administrator. Your new temporary password is: {$newPassword}. Please change it after logging in.",
            ['reset_by_admin' => auth()->id()],
            'high'
        );

        // You could also send an email here
        // Mail::to($user->email)->send(new PasswordResetMail($user, $newPassword));

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', "Password reset successfully. New password: {$newPassword}");
    }
}
