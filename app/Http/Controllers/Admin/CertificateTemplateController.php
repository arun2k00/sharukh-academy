<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CertificateTemplateRequest;
use App\Models\CertificateTemplate;
use App\Services\AdminActivityLogger;
use App\Services\Certificates\CertificateImageService;
use App\Services\Certificates\CertificateTemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CertificateTemplateController extends Controller
{
    public function __construct(
        private readonly CertificateTemplateService $templates,
        private readonly CertificateImageService $renderer,
        private readonly AdminActivityLogger $activity,
    ) {}

    public function index(): View
    {
        Gate::authorize('viewAny', CertificateTemplate::class);

        return view('admin.certificate-templates.index', [
            'templates' => CertificateTemplate::query()->withCount('courses')->latest('id')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', CertificateTemplate::class);

        return view('admin.certificate-templates.create', [
            'template' => new CertificateTemplate(['is_active' => true, 'show_qr_code' => false]),
            'fieldLabels' => CertificateTemplate::fieldLabels(),
        ]);
    }

    public function store(CertificateTemplateRequest $request): RedirectResponse
    {
        $image = $this->templates->storeImage($request->file('image'));

        $template = new CertificateTemplate([
            'name' => $request->string('name')->toString(),
            'image_path' => $image['path'],
            'image_width' => $image['width'],
            'image_height' => $image['height'],
            'is_active' => $request->boolean('is_active', true),
            'show_qr_code' => $request->boolean('show_qr_code'),
            'fields' => $this->templates->buildFieldConfig($request->input('fields')),
        ]);

        $this->applySignature($template, $request);
        $template->save();

        $this->activity->log('certificate_template.created', $template, "Created certificate template \"{$template->name}\"");

        return redirect()->route('admin.certificate-templates.edit', $template)
            ->with('status', $image['warning'] ?? 'Certificate template created.');
    }

    public function edit(CertificateTemplate $certificateTemplate): View
    {
        Gate::authorize('update', $certificateTemplate);

        $fields = [];

        foreach ([...CertificateTemplate::REQUIRED_FIELDS, ...CertificateTemplate::OPTIONAL_FIELDS] as $key) {
            $fields[$key] = [
                ...$certificateTemplate->fieldConfig($key),
                'enabled' => array_key_exists($key, $certificateTemplate->fields ?? []),
            ];
        }

        // Required fields are always positioned, whether or not the admin
        // has touched them yet.
        foreach (CertificateTemplate::REQUIRED_FIELDS as $key) {
            $fields[$key]['enabled'] = true;
        }

        return view('admin.certificate-templates.edit', [
            'template' => $certificateTemplate,
            'fieldLabels' => CertificateTemplate::fieldLabels(),
            'fonts' => CertificateImageService::availableFonts(),
            'initialFields' => $fields,
        ]);
    }

    public function update(CertificateTemplateRequest $request, CertificateTemplate $certificateTemplate): RedirectResponse
    {
        $certificateTemplate->fill([
            'name' => $request->string('name')->toString(),
            'is_active' => $request->boolean('is_active', true),
            'show_qr_code' => $request->boolean('show_qr_code'),
            'fields' => $this->templates->buildFieldConfig($request->input('fields')),
        ]);

        $warning = null;

        if ($request->hasFile('image')) {
            $this->templates->deleteImage($certificateTemplate->image_path);
            $image = $this->templates->storeImage($request->file('image'));
            $certificateTemplate->image_path = $image['path'];
            $certificateTemplate->image_width = $image['width'];
            $certificateTemplate->image_height = $image['height'];
            $warning = $image['warning'];
        }

        $this->applySignature($certificateTemplate, $request);
        $certificateTemplate->save();

        $this->activity->log('certificate_template.updated', $certificateTemplate, "Updated certificate template \"{$certificateTemplate->name}\"");

        return redirect()->route('admin.certificate-templates.edit', $certificateTemplate)
            ->with('status', $warning ?? 'Certificate template saved.');
    }

    public function destroy(CertificateTemplate $certificateTemplate): RedirectResponse
    {
        Gate::authorize('delete', $certificateTemplate);

        if ($certificateTemplate->courses()->exists()) {
            return back()->with('error', 'This template is assigned to one or more courses. Reassign them first.');
        }

        $this->templates->deleteImage($certificateTemplate->image_path);
        $this->templates->deleteImage($certificateTemplate->signature_path);

        $name = $certificateTemplate->name;
        $certificateTemplate->delete();

        $this->activity->log('certificate_template.deleted', null, "Deleted certificate template \"{$name}\"");

        return redirect()->route('admin.certificate-templates.index')->with('status', 'Template deleted.');
    }

    /**
     * Streams the template rendered with the worked example from the
     * specification, so what the admin sees while editing is exactly what a
     * student's certificate will look like.
     */
    public function preview(CertificateTemplate $certificateTemplate): Response
    {
        Gate::authorize('view', $certificateTemplate);

        $bytes = $this->renderer->render($certificateTemplate, $this->renderer->sampleData());

        return response($bytes, 200, ['Content-Type' => 'image/png']);
    }

    private function applySignature(CertificateTemplate $template, CertificateTemplateRequest $request): void
    {
        if ($request->hasFile('signature')) {
            $this->templates->deleteImage($template->signature_path);
            $template->signature_path = $this->templates->storeSignature($request->file('signature'));
        }

        if ($request->filled('signature_x')) {
            $template->signature_x = $request->float('signature_x');
            $template->signature_y = $request->float('signature_y');
            $template->signature_width = $request->float('signature_width');
        }
    }
}
