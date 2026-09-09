<x-layouts.admin :title="'Edit — '.$template->name" heading="Edit certificate template">
    <x-admin.page-header :title="$template->name" :back="route('admin.certificate-templates.index')" back-label="All templates"
                         description="Drag a label onto the artwork, or type exact numbers on the right. Both stay in sync." />

    <div x-data="certificateEditor({
            fields: {{ Js::from($initialFields) }},
            showQrCode: {{ Js::from($template->show_qr_code) }},
            signature: {{ Js::from([
                'enabled' => $template->signature_path !== null,
                'x' => (float) ($template->signature_x ?? 50),
                'y' => (float) ($template->signature_y ?? 78),
                'width' => (float) ($template->signature_width ?? 18),
            ]) }},
            previewBaseUrl: {{ Js::from(route('admin.certificate-templates.preview', $template)) }},
        })"
         @mousemove.window="onMove($event)" @mouseup.window="stopDrag()"
         @touchmove.window="onMove($event)" @touchend.window="stopDrag()">

        <form method="POST" action="{{ route('admin.certificate-templates.update', $template) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid gap-6 lg:grid-cols-12">
                {{-- Visual canvas --}}
                <div class="lg:col-span-7">
                    <x-ui.panel title="Position the text" :padded="false">
                        <div class="relative bg-ink-100 select-none" x-ref="canvas"
                             style="aspect-ratio: {{ $template->image_width }} / {{ $template->image_height }};">
                            <img src="{{ $template->image_url }}" alt="" class="pointer-events-none absolute inset-0 size-full object-contain" draggable="false">

                            <template x-for="key in visibleKeys()" :key="key">
                                <div class="absolute -translate-x-1/2 -translate-y-1/2 cursor-move touch-none border border-pine-700 bg-white/95 px-2 py-1 text-[10px] font-medium tracking-wide text-ink-800 uppercase shadow-sm"
                                     :style="`left: ${fields[key].x}%; top: ${fields[key].y}%;`"
                                     @mousedown.prevent="startDrag(key)" @touchstart.prevent="startDrag(key)"
                                     x-text="labels[key]"></div>
                            </template>

                            <template x-if="showQrCode">
                                <div class="pointer-events-none absolute right-[2%] bottom-[2%] flex aspect-square w-[9%] items-center justify-center border border-dashed border-ink-400 bg-white/70 text-[8px] text-ink-500">
                                    QR
                                </div>
                            </template>

                            <template x-if="signature.enabled">
                                <div class="absolute -translate-x-1/2 -translate-y-1/2 cursor-move touch-none border border-brass-600 bg-white/95 px-2 py-1 text-[10px] font-medium tracking-wide text-brass-700 uppercase shadow-sm"
                                     :style="`left: ${signature.x}%; top: ${signature.y}%; width: ${signature.width}%;`"
                                     @mousedown.prevent="startDragSignature()" @touchstart.prevent="startDragSignature()">
                                    Signature
                                </div>
                            </template>
                        </div>
                    </x-ui.panel>

                    <div class="mt-6">
                        <x-ui.panel title="Preview with sample data" description="Renders exactly what a student's certificate will look like.">
                            <img :src="previewUrl" alt="Certificate preview" class="w-full border border-ink-200">
                            <x-ui.button type="button" variant="outline" size="sm" class="mt-4" @click="refreshPreview()">
                                Refresh preview
                            </x-ui.button>
                        </x-ui.panel>
                    </div>
                </div>

                {{-- Controls --}}
                <div class="space-y-6 lg:col-span-5">
                    <x-ui.panel title="Template">
                        <div class="space-y-5">
                            <x-ui.field label="Name" for="name" :error="$errors->first('name')" required>
                                <x-ui.input id="name" name="name" value="{{ old('name', $template->name) }}" required
                                            :invalid="$errors->has('name')" />
                            </x-ui.field>

                            <img src="{{ $template->image_url }}" alt="" class="aspect-[16/9] w-full border border-ink-200 object-cover">
                            <x-ui.field label="Replace artwork" for="image" :error="$errors->first('image')"
                                        hint="{{ $template->image_width }}×{{ $template->image_height }}px currently.">
                                <input id="image" name="image" type="file" accept="image/png,image/jpeg"
                                       class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                            </x-ui.field>

                            <x-ui.checkbox name="is_active" value="1" label="Active"
                                           :checked="(bool) old('is_active', $template->is_active)" />

                            <x-ui.checkbox name="show_qr_code" value="1" label="Include a verification QR code"
                                           x-model="showQrCode"
                                           :checked="(bool) old('show_qr_code', $template->show_qr_code)" />
                        </div>
                    </x-ui.panel>

                    @foreach ([...\App\Models\CertificateTemplate::REQUIRED_FIELDS, ...\App\Models\CertificateTemplate::OPTIONAL_FIELDS] as $key)
                        @php $isOptional = in_array($key, \App\Models\CertificateTemplate::OPTIONAL_FIELDS, true); @endphp
                        <x-ui.panel :title="$fieldLabels[$key]">
                            @if ($isOptional)
                                <label class="mb-4 flex items-center gap-2.5 text-sm text-ink-800">
                                    <input type="checkbox" name="fields[{{ $key }}][enabled]" value="1"
                                           x-model="fields.{{ $key }}.enabled"
                                           class="size-4 border-ink-300 text-pine-700 focus:ring-pine-600">
                                    Show this field on the certificate
                                </label>
                            @endif

                            <div x-show="fields.{{ $key }}.enabled" x-cloak class="grid grid-cols-2 gap-4">
                                <x-ui.field label="Horizontal (%)">
                                    <x-ui.input type="number" step="0.1" min="0" max="100"
                                                name="fields[{{ $key }}][x]" x-model.number="fields.{{ $key }}.x" />
                                </x-ui.field>
                                <x-ui.field label="Vertical (%)">
                                    <x-ui.input type="number" step="0.1" min="0" max="100"
                                                name="fields[{{ $key }}][y]" x-model.number="fields.{{ $key }}.y" />
                                </x-ui.field>
                                <x-ui.field label="Font">
                                    <x-ui.select name="fields[{{ $key }}][font]" x-model="fields.{{ $key }}.font">
                                        @foreach ($fonts as $font)
                                            <option value="{{ $font }}">{{ ucfirst(str_replace('-', ' ', $font)) }}</option>
                                        @endforeach
                                    </x-ui.select>
                                </x-ui.field>
                                <x-ui.field label="Size (px)">
                                    <x-ui.input type="number" min="8" max="400"
                                                name="fields[{{ $key }}][font_size]" x-model.number="fields.{{ $key }}.font_size" />
                                </x-ui.field>
                                <x-ui.field label="Alignment">
                                    <x-ui.select name="fields[{{ $key }}][align]" x-model="fields.{{ $key }}.align">
                                        <option value="left">Left</option>
                                        <option value="center">Center</option>
                                        <option value="right">Right</option>
                                    </x-ui.select>
                                </x-ui.field>
                                <x-ui.field label="Color">
                                    <input type="color" name="fields[{{ $key }}][color]" x-model="fields.{{ $key }}.color"
                                           class="h-[42px] w-full border border-ink-300">
                                </x-ui.field>
                            </div>
                        </x-ui.panel>
                    @endforeach

                    <x-ui.panel title="Signature image" description="Optional. Position it like the text fields above.">
                        <label class="mb-4 flex items-center gap-2.5 text-sm text-ink-800">
                            <input type="checkbox" x-model="signature.enabled" class="size-4 border-ink-300 text-pine-700 focus:ring-pine-600">
                            Show a signature image
                        </label>

                        <div x-show="signature.enabled" x-cloak class="space-y-4">
                            @if ($template->signature_url)
                                <img src="{{ $template->signature_url }}" alt="" class="h-16 border border-ink-200 bg-white object-contain p-2">
                            @endif
                            <x-ui.field label="Signature file" for="signature">
                                <input id="signature" name="signature" type="file" accept="image/png,image/jpeg"
                                       class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                            </x-ui.field>
                            <div class="grid grid-cols-3 gap-4">
                                <x-ui.field label="X (%)">
                                    <x-ui.input type="number" step="0.1" min="0" max="100" name="signature_x" x-model.number="signature.x" />
                                </x-ui.field>
                                <x-ui.field label="Y (%)">
                                    <x-ui.input type="number" step="0.1" min="0" max="100" name="signature_y" x-model.number="signature.y" />
                                </x-ui.field>
                                <x-ui.field label="Width (%)">
                                    <x-ui.input type="number" step="0.1" min="2" max="60" name="signature_width" x-model.number="signature.width" />
                                </x-ui.field>
                            </div>
                        </div>
                    </x-ui.panel>

                    <div class="flex items-center gap-3">
                        <x-ui.button type="submit">Save template</x-ui.button>
                        <x-ui.button :href="route('admin.certificate-templates.index')" variant="ghost">Done</x-ui.button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-10 border-t border-ink-200 pt-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border border-red-200 bg-red-50 p-4">
            <p class="text-sm text-ink-700">Delete this template. Courses using it fall back to the plain printable certificate — reassign them first.</p>
            <x-admin.delete-form :action="route('admin.certificate-templates.destroy', $template)"
                                 confirm="Delete this template?" label="Delete template" />
        </div>
    </div>
</x-layouts.admin>
