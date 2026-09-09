<x-layouts.admin :title="$liveClass->title" heading="Edit live class">
    <x-admin.page-header :title="$liveClass->title" :back="route('admin.live-classes.index')"
                         back-label="All live classes" />

    <form method="POST" action="{{ route('admin.live-classes.update', $liveClass) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.live-classes._form')

        <div class="mt-6 flex items-center gap-3">
            <x-ui.button type="submit">Save changes</x-ui.button>
            <x-ui.button :href="route('admin.live-classes.index')" variant="ghost">Cancel</x-ui.button>
        </div>
    </form>

    <div class="mt-10 grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <x-ui.panel title="Class materials"
                        description="Downloadable by students enrolled in the parent course." :padded="false">
                @if ($liveClass->resources->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-ink-500">No materials attached.</p>
                @else
                    <ul class="divide-y divide-ink-200">
                        @foreach ($liveClass->resources as $resource)
                            <li class="flex items-center gap-4 px-5 py-3">
                                <x-ui.icon name="file-text" class="size-5 shrink-0 text-ink-400" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm text-ink-900">{{ $resource->title }}</p>
                                    <p class="truncate text-[0.75rem] text-ink-500">
                                        {{ $resource->original_name }} · {{ $resource->size_label }}
                                    </p>
                                </div>
                                <x-admin.delete-form :action="route('admin.live-classes.resources.destroy', $resource)"
                                                     confirm="Delete this file permanently?" label="Remove" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.panel>
        </div>

        <div class="lg:col-span-4">
            <x-ui.panel title="Upload material">
                <form method="POST" action="{{ route('admin.live-classes.resources.store', $liveClass) }}"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <x-ui.field label="Title" for="resource_title" :error="$errors->first('title')" required>
                        <x-ui.input id="resource_title" name="title" required :invalid="$errors->has('title')" />
                    </x-ui.field>

                    <x-ui.field label="File" for="resource_file" :error="$errors->first('file')" required>
                        <input id="resource_file" name="file" type="file" required
                               class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                    </x-ui.field>

                    <x-ui.button type="submit" class="w-full">Upload</x-ui.button>
                </form>
            </x-ui.panel>
        </div>
    </div>
</x-layouts.admin>
