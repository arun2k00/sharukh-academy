<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        return view('student.certificates.index', [
            'certificates' => Certificate::query()
                ->where('user_id', $request->user()->id)
                ->with('course.certificateTemplate')
                ->latest('issued_at')
                ->get(),
        ]);
    }

    public function show(Certificate $certificate): View
    {
        Gate::authorize('view', $certificate);

        $certificate->load('course.certificateTemplate');

        return view('student.certificates.show', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Standalone printable layout. Browsers turn this into a PDF via "Save as
     * PDF"; a server-side PDF renderer can be dropped in later without the
     * route or the markup changing.
     */
    public function print(Certificate $certificate): View
    {
        Gate::authorize('view', $certificate);

        $certificate->load('course.certificateTemplate');

        return view('student.certificates.print', [
            'certificate' => $certificate,
        ]);
    }
}
