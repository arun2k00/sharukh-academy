<x-layouts.admin title="Upload certificate template" heading="Upload certificate template">
    <x-admin.page-header title="Upload artwork" :back="route('admin.certificate-templates.index')" back-label="All templates" />

    <x-ui.alert variant="info" class="mb-6">
        <p class="font-medium text-ink-900">Recommended: A4 landscape</p>
        <p class="mt-1">Ratio 1.414:1 (297 × 210mm), at least 2000px on the long edge. A different ratio still works —
            you will just need to adjust text positions more carefully on the next screen.</p>
    </x-ui.alert>

    <div class="max-w-xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.certificate-templates.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <x-ui.field label="Template name" for="name" :error="$errors->first('name')" required>
                    <x-ui.input id="name" name="name" value="{{ old('name') }}" required
                                placeholder="Classic gold border" :invalid="$errors->has('name')" />
                </x-ui.field>

                <x-ui.field label="Certificate artwork" for="image" :error="$errors->first('image')"
                            hint="PNG or JPG, up to 10 MB." required>
                    <input id="image" name="image" type="file" accept="image/png,image/jpeg" required
                           class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                </x-ui.field>

                <x-ui.checkbox name="is_active" value="1" label="Active" hint="Available to assign to courses."
                               :checked="true" />

                <x-ui.checkbox name="show_qr_code" value="1" label="Include a verification QR code"
                               hint="Placed in the bottom-right corner, linking to the public verification page." />

                {{-- Text positions are configured on the next screen, once the
                     real image is available to position against. --}}
                <input type="hidden" name="fields[student_name][x]" value="50">
                <input type="hidden" name="fields[student_name][y]" value="45">
                <input type="hidden" name="fields[student_name][font]" value="serif">
                <input type="hidden" name="fields[student_name][font_size]" value="120">
                <input type="hidden" name="fields[student_name][color]" value="#1a1a18">
                <input type="hidden" name="fields[student_name][align]" value="center">
                <input type="hidden" name="fields[course_name][x]" value="50">
                <input type="hidden" name="fields[course_name][y]" value="58">
                <input type="hidden" name="fields[course_name][font]" value="serif-bold">
                <input type="hidden" name="fields[course_name][font_size]" value="64">
                <input type="hidden" name="fields[course_name][color]" value="#1a1a18">
                <input type="hidden" name="fields[course_name][align]" value="center">
                <input type="hidden" name="fields[completion_date][x]" value="22">
                <input type="hidden" name="fields[completion_date][y]" value="86">
                <input type="hidden" name="fields[completion_date][font]" value="sans">
                <input type="hidden" name="fields[completion_date][font_size]" value="34">
                <input type="hidden" name="fields[completion_date][color]" value="#55554f">
                <input type="hidden" name="fields[completion_date][align]" value="center">
                <input type="hidden" name="fields[certificate_id][x]" value="78">
                <input type="hidden" name="fields[certificate_id][y]" value="86">
                <input type="hidden" name="fields[certificate_id][font]" value="sans">
                <input type="hidden" name="fields[certificate_id][font_size]" value="34">
                <input type="hidden" name="fields[certificate_id][color]" value="#55554f">
                <input type="hidden" name="fields[certificate_id][align]" value="center">

                <div class="flex items-center gap-3">
                    <x-ui.button type="submit">Upload and position text</x-ui.button>
                    <x-ui.button :href="route('admin.certificate-templates.index')" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
