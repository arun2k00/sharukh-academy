<x-layouts.admin :title="'Edit module — '.$module->title" heading="Edit module">
    <x-admin.page-header :title="$module->title" :back="route('admin.courses.curriculum', $course)"
                         back-label="Back to curriculum" :description="$course->title" />

    <div class="max-w-2xl">
        <x-ui.panel>
            <form method="POST" action="{{ route('admin.modules.update', $module) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-ui.field label="Module title" for="title" :error="$errors->first('title')" required>
                    <x-ui.input id="title" name="title" value="{{ old('title', $module->title) }}" required
                                :invalid="$errors->has('title')" />
                </x-ui.field>

                <x-ui.field label="Description" for="description" :error="$errors->first('description')">
                    <x-ui.textarea id="description" name="description" rows="3"
                                   :invalid="$errors->has('description')">{{ old('description', $module->description) }}</x-ui.textarea>
                </x-ui.field>

                <x-ui.field label="Position" for="sort_order" :error="$errors->first('sort_order')"
                            hint="Lower numbers appear first. Use the arrows on the curriculum screen for quick moves.">
                    <x-ui.input id="sort_order" name="sort_order" type="number" min="0"
                                value="{{ old('sort_order', $module->sort_order) }}" :invalid="$errors->has('sort_order')" />
                </x-ui.field>

                <div class="flex items-center gap-3">
                    <x-ui.button type="submit">Save module</x-ui.button>
                    <x-ui.button :href="route('admin.courses.curriculum', $course)" variant="ghost">Cancel</x-ui.button>
                </div>
            </form>
        </x-ui.panel>
    </div>
</x-layouts.admin>
