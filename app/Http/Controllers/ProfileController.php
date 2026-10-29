<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\VolunteerStatus;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\VolunteerApplicationRequest;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $this->authorize('view', $request->user());

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->authorize('update', $request->user());

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function applyVolunteer(VolunteerApplicationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorize('update', $user);
        $user->volunteer_status = VolunteerStatus::Pending;
        $user->save();
        foreach (User::where('role', Role::Admin->value)->get() as $admin) {
            UserNotification::create(['user_id' => $admin->id, 'message' => 'Pengajuan relawan: '.$user->name.' ('.$user->email.'). Alasan: '.$request->validated('motivation')]);
        }

        return Redirect::route('profile.edit')->with('status', 'Pengajuan relawan dikirim untuk ditinjau admin.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->authorize('delete', $request->user());

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
