<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Profile picture upload to Cloudinary inside folder WitExpenseTracker/{username}/profile
        if ($request->hasFile('profile_pic')) {
            $uploadResult = $this->cloudinaryService->upload(
                $request->file('profile_pic'),
                $user->username ?? $user->name,
                'profile'
            );

            if ($uploadResult) {
                $validated['profile_pic'] = $uploadResult['url'];
            }
        }

        $user->fill([
            'name' => $validated['name'],
            'username' => strtolower(trim($validated['username'] ?? $user->username)),
            'email' => $validated['email'],
            'monthly_budget' => $validated['monthly_budget'] ?? $user->monthly_budget,
            'budget_warn_limit' => $validated['budget_warn_limit'] ?? $user->budget_warn_limit,
            'theme_color' => $validated['theme_color'] ?? $user->theme_color,
            'dark_mode' => $request->has('dark_mode') ? $request->boolean('dark_mode') : $user->dark_mode,
        ]);

        if (isset($validated['profile_pic'])) {
            $user->profile_pic = $validated['profile_pic'];
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profile and preferences updated successfully!');
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Password updated successfully!');
    }
}
