<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\Certificates\CertificateImageService;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * Streams the composited certificate image (§1).
 *
 * Reached from two routes: an authenticated one (`certificates.image`, owner
 * or admin only) and the public verification page's `certificates.verify-image`
 * — which is intentionally open, since it renders nothing the public
 * verification page does not already state in text.
 */
class CertificateImageController extends Controller
{
    public function __invoke(
        Request $request,
        Certificate $certificate,
        CertificateImageService $renderer,
        CertificateService $certificates,
    ): Response {
        // Only the private route is owner-gated. The public verification page
        // is open by design, and checking ownership there would 403 any signed-in
        // visitor verifying someone else's certificate (an employer, say).
        if ($request->routeIs('certificates.image')) {
            Gate::authorize('view', $certificate);
        }

        $certificate->loadMissing('course.certificateTemplate');
        $template = $certificate->course->certificateTemplate;

        abort_unless($template !== null, 404, 'No certificate artwork has been configured for this course yet.');

        $bytes = $renderer->render($template, $certificates->renderData($certificate));

        $disposition = $request->boolean('download') ? 'attachment' : 'inline';
        $filename = 'certificate-'.$certificate->certificate_number.'.png';

        return response($bytes, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
