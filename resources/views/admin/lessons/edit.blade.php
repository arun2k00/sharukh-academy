<x-layouts.admin :title="'Edit lesson — '.$lesson->title" heading="Edit lesson">
    <x-admin.page-header :title="$lesson->title" :back="route('admin.courses.curriculum', $course)"
                         back-label="Back to curriculum" :description="$course->title" />

    <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}">
        @csrf
        @method('PUT')
        @include('admin.lessons._form')

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-ui.button type="submit">Save lesson</x-ui.button>
            <x-ui.button :href="route('admin.courses.curriculum', $course)" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>

    {{-- Resources are managed separately so an upload never loses unsaved lesson edits. --}}
    <div class="mt-10 grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <x-ui.panel title="Lesson resources"
                        description="Files students can download. Only enrolled students can reach them." :padded="false">
                @if ($lesson->resources->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-ink-500">No files attached yet.</p>
                @else
                    <ul class="divide-y divide-ink-200">
                        @foreach ($lesson->resources as $resource)
                            <li class="flex items-center gap-4 px-5 py-3">
                                <x-ui.icon name="file-text" class="size-5 shrink-0 text-ink-400" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm text-ink-900">{{ $resource->title }}</p>
                                    <p class="truncate text-[0.75rem] text-ink-500">
                                        {{ $resource->original_name }} · {{ $resource->size_label }} ·
                                        {{ $resource->downloads_count }} {{ str('download')->plural($resource->downloads_count) }}
                                    </p>
                                </div>
                                <x-admin.delete-form :action="route('admin.lessons.resources.destroy', $resource)"
                                                     confirm="Delete this file permanently?" label="Remove" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.panel>
        </div>

        <div class="lg:col-span-4">
            <x-ui.panel title="Upload a resource">
                <form method="POST" action="{{ route('admin.lessons.resources.store', $lesson) }}"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <x-ui.field label="Title" for="resource_title" :error="$errors->first('title')" required>
                        <x-ui.input id="resource_title" name="title" required placeholder="Market sizing worksheet"
                                    :invalid="$errors->has('title')" />
                    </x-ui.field>

                    <x-ui.field label="File" for="resource_file" :error="$errors->first('file')"
                                hint="PDF, Office documents, images or ZIP up to 20 MB." required>
                        <input id="resource_file" name="file" type="file" required
                               class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                    </x-ui.field>

                    <x-ui.button type="submit" class="w-full">Upload</x-ui.button>
                </form>
            </x-ui.panel>
        </div>
    </div>

    <div class="mt-10 border-t border-ink-200 pt-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border border-red-200 bg-red-50 p-4">
            <p class="text-sm text-ink-700">
                Deleting a lesson also removes its resources and every student's progress record for it.
            </p>
            <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}"
                  onsubmit="return confirm('Delete this lesson permanently?')">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger" size="sm">Delete lesson</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.admin>
