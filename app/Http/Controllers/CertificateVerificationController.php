<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\View\View;

/**
 * Public lookup so an employer can confirm a certificate is genuine.
 *
 * Only the certificate number, holder name, course and date are exposed.
 */
class CertificateVerificationController extends Controller
{
    public function __invoke(Certificate $certificate): View
    {
        $certificate->load('course.certificateTemplate');

        return view('public.certificate-verification', [
            'certificate' => $certificate,
        ]);
    }
}
