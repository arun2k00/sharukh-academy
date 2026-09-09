<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Certificate::class);

        $certificates = Certificate::query()
            ->when($request->filled('q'), fn ($query) => $query
                ->where('certificate_number', 'like', '%'.$request->string('q').'%')
                ->orWhere('student_name', 'like', '%'.$request->string('q').'%'))
            ->with(['user', 'course'])
            ->latest('issued_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.certificates.index', [
            'certificates' => $certificates,
        ]);
    }

    /**
     * Revoke a certificate. It is reissued automatically if the student still
     * meets the course requirements and opens the course again.
     */
    public function destroy(Certificate $certificate): RedirectResponse
    {
        Gate::authorize('delete', $certificate);

        $certificate->delete();

        return back()->with('status', 'Certificate revoked.');
    }
}
