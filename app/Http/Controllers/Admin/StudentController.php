<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentRequest;
use App\Models\User;
use App\Services\ResourceStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $students = User::query()
            ->search($request->string('q')->toString())
            ->when($request->string('role')->toString() === 'admin', fn ($query) => $query->admins())
            ->when($request->string('role')->toString() === 'student', fn ($query) => $query->students())
            ->withCount(['enrollments', 'certificates'])
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.students.index', [
            'students' => $students,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('viewAny', User::class);

        return view('admin.students.create', [
            'student' => new User(['role' => UserRole::Student]),
        ]);
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $student = new User($request->safe()->only(['name', 'email', 'password', 'phone', 'bio']));
        $student->role = UserRole::from($request->string('role')->toString());

        if ($request->boolean('email_verified')) {
            $student->email_verified_at = now();
        }

        if ($request->hasFile('avatar')) {
            $student->avatar_path = $this->storage->storeImage($request->file('avatar'), 'avatars');
        }

        $student->save();

        return redirect()->route('admin.students.show', $student)
            ->with('status', 'Account created.');
    }

    public function show(User $student): View
    {
        Gate::authorize('view', $student);

        $student->load([
            'enrollments.course',
            'certificates.course',
        ]);

        return view('admin.students.show', [
            'student' => $student,
            'lessonsCompleted' => $student->lessonProgress()->where('is_completed', true)->count(),
        ]);
    }

    public function edit(User $student): View
    {
        Gate::authorize('update', $student);

        return view('admin.students.edit', [
            'student' => $student,
        ]);
    }

    public function update(StudentRequest $request, User $student): RedirectResponse
    {
        $student->fill($request->safe()->only(['name', 'email', 'phone', 'bio']));
        $student->role = UserRole::from($request->string('role')->toString());

        if ($request->filled('password')) {
            $student->password = $request->string('password')->toString();
        }

        $student->email_verified_at = $request->boolean('email_verified')
            ? ($student->email_verified_at ?? now())
            : null;

        if ($request->hasFile('avatar')) {
            $this->storage->deleteImage($student->avatar_path);
            $student->avatar_path = $this->storage->storeImage($request->file('avatar'), 'avatars');
        }

        $student->save();

        return redirect()->route('admin.students.show', $student)
            ->with('status', 'Account updated.');
    }

    public function destroy(User $student): RedirectResponse
    {
        Gate::authorize('delete', $student);

        // Soft delete: enrolments, progress and certificates stay auditable.
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('status', 'Account removed.');
    }
}
