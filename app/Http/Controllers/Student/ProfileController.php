<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateProfileRequest;
use App\Services\ResourceStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('student.profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request, ResourceStorageService $storage): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only(['name', 'phone', 'bio']));

        if ($request->hasFile('avatar')) {
            $storage->deleteImage($user->avatar_path);
            $user->avatar_path = $storage->storeImage($request->file('avatar'), 'avatars');
        }

        $user->save();

        return back()->with('status', 'Your profile has been updated.');
    }
}
