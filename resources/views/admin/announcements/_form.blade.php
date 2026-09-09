<div class="max-w-2xl space-y-5">
    <x-ui.field label="Title" for="title" :error="$errors->first('title')" required>
        <x-ui.input id="title" name="title" value="{{ old('title', $announcement->title) }}" required :invalid="$errors->has('title')" />
    </x-ui.field>

    <x-ui.field label="Message" for="body" :error="$errors->first('body')" required>
        <x-ui.textarea id="body" name="body" rows="5" required :invalid="$errors->has('body')">{{ old('body', $announcement->body) }}</x-ui.textarea>
    </x-ui.field>

    <x-ui.field label="Audience" for="course_id" :error="$errors->first('course_id')"
                hint="Leave as 'All students' for a sitewide announcement.">
        <x-ui.select id="course_id" name="course_id" :invalid="$errors->has('course_id')">
            <option value="">All students</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected((int) old('course_id', $announcement->course_id) === $course->id)>
                    Students enrolled in {{ $course->title }}
                </option>
            @endforeach
        </x-ui.select>
    </x-ui.field>
</div>
