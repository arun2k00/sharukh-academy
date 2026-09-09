<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Course;
use App\Models\Testimonial;
use App\Services\ResourceStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function __construct(
        private readonly ResourceStorageService $storage,
    ) {}

    public function index(): View
    {
        return view('admin.testimonials.index', [
            'testimonials' => Testimonial::query()->with('course')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.testimonials.create', [
            'testimonial' => new Testimonial(['rating' => 5, 'is_published' => true]),
            'courses' => Course::query()->orderBy('title')->get(),
        ]);
    }

    public function store(TestimonialRequest $request): RedirectResponse
    {
        $testimonial = new Testimonial($request->safe()->except('avatar'));

        if ($request->hasFile('avatar')) {
            $testimonial->avatar_path = $this->storage->storeImage($request->file('avatar'), 'testimonials');
        }

        $testimonial->save();

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial added.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.edit', [
            'testimonial' => $testimonial,
            'courses' => Course::query()->orderBy('title')->get(),
        ]);
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->fill($request->safe()->except('avatar'));

        if ($request->hasFile('avatar')) {
            $this->storage->deleteImage($testimonial->avatar_path);
            $testimonial->avatar_path = $this->storage->storeImage($request->file('avatar'), 'testimonials');
        }

        $testimonial->save();

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $this->storage->deleteImage($testimonial->avatar_path);
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('status', 'Testimonial removed.');
    }
}
