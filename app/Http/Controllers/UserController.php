<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function show(User $user)
    {
        $reviews = $user->reviews()->with('user')->latest()->paginate(10);

        return view('users.show', ['user' => $user, 'reviews' => $reviews]);
    }

    public function edit(Request $request, User $user)
    {
        $reviews = Auth::user()->reviews()->withCount('comments')->latest()->paginate(8);
        $comments = Auth::user()->comments()->with(['review', 'user'])->latest()->paginate(10);
        $menu = in_array($request->menu, ['menu1', 'menu2', 'menu3', 'menu4'], true) ? $request->menu : 'menu1';

        return view('users.edit', ['reviews' => $reviews, 'comments' => $comments, 'menu' => $menu]);
    }

    public function store(Request $request) {}

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();
        $oldAvatar = null;
        $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            if ($path !== false) {
                $oldAvatar = $user->avatar;
                $user->avatar = $path;
            }
        }

        $user->fill(array_intersect_key($validated, array_flip(['name', 'nickname', 'email', 'bio', 'hide_spoilers_default', 'show_views_count', 'private_profile', 'hide_email', 'newsletter', 'theme_preference'])));

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')->with('success', __('dashboard.email_changed'));
        }

        if (filled(array_intersect_key($validated, array_flip(['hide_spoilers_default', 'show_views_count', 'private_profile', 'hide_email', 'newsletter', 'theme_preference'])))) {
            return redirect()->route('users.edit', ['menu' => 'menu4'])->with('success', __('dashboard.settings_updated'));
        }

        return redirect('/')->with('success', __('dashboard.profile_updated'));
    }

    public function exportData(Request $request)
    {
        $user = Auth::user();

        $data = [
            'user' => [
                'name' => $user->name,
                'nickname' => $user->nickname,
                'email' => $user->email,
                'bio' => $user->bio,
                'created_at' => $user->created_at->toISOString(),
            ],
            'reviews' => $user->reviews->map(function ($review) {
                return [
                    'title' => $review->title,
                    'body' => $review->body,
                    'rating' => $review->rating,
                    'recommendation' => $review->recommendation,
                    'contains_spoilers' => $review->contains_spoilers,
                    'game_name' => $review->game_name,
                    'platform_playable' => $review->platform_playable,
                    'created_at' => $review->created_at->toISOString(),
                ];
            }),
            'comments' => $user->comments->map(function ($comment) {
                return [
                    'body' => $comment->body,
                    'review_title' => $comment->review->title ?? null,
                    'created_at' => $comment->created_at->toISOString(),
                ];
            }),
            'exported_at' => now()->toISOString(),
        ];

        $filename = "pixelrank_export_{$user->nickname}_".now()->format('Y-m-d').'.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', $user);

        // Deletar reviews do usuário
        $user->reviews()->delete();

        // Deletar comentários do usuário
        $user->comments()->delete();

        // Deletar avatar se existir
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Deletar usuário
        $user->delete();

        // Logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', __('dashboard.delete_account_confirm'));
    }
}
