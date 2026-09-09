<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ResourceStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function __invoke(Request $request, ResourceStorageService $storage): RedirectResponse
    {
        $user = $request->user();

        $storage->deleteImage($user->avatar_path);
        $user->forceFill(['avatar_path' => null])->save();

        return back()->with('status', 'Profile photo removed.');
    }
}
