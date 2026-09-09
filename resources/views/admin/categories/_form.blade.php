<div class="space-y-5">
    <x-ui.field label="Name" for="name" :error="$errors->first('name')" required>
        <x-ui.input id="name" name="name" value="{{ old('name', $category->name) }}" required :invalid="$errors->has('name')" />
    </x-ui.field>

    <x-ui.field label="URL slug" for="slug" :error="$errors->first('slug')" hint="Leave blank to generate it from the name.">
        <x-ui.input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" :invalid="$errors->has('slug')" />
    </x-ui.field>

    <x-ui.field label="Description" for="description" :error="$errors->first('description')"
                hint="Shown at the top of the filtered catalogue.">
        <x-ui.input id="description" name="description" value="{{ old('description', $category->description) }}"
                    :invalid="$errors->has('description')" />
    </x-ui.field>

    <x-ui.field label="Position" for="sort_order" :error="$errors->first('sort_order')">
        <x-ui.input id="sort_order" name="sort_order" type="number" min="0"
                    value="{{ old('sort_order', $category->sort_order ?? 0) }}" :invalid="$errors->has('sort_order')" />
    </x-ui.field>
</div>
