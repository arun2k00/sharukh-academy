<div class="space-y-5">
    <x-ui.field label="Question" for="question" :error="$errors->first('question')" required>
        <x-ui.input id="question" name="question" value="{{ old('question', $faq->question) }}" required
                    :invalid="$errors->has('question')" />
    </x-ui.field>

    <x-ui.field label="Answer" for="answer" :error="$errors->first('answer')" required>
        <x-ui.textarea id="answer" name="answer" rows="6" required
                       :invalid="$errors->has('answer')">{{ old('answer', $faq->answer) }}</x-ui.textarea>
    </x-ui.field>

    <x-ui.field label="Course" for="course_id" :error="$errors->first('course_id')"
                hint="General questions appear on the homepage and on every course page.">
        <x-ui.select id="course_id" name="course_id" :invalid="$errors->has('course_id')">
            <option value="">General question</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected((int) old('course_id', $faq->course_id) === $course->id)>{{ $course->title }}</option>
            @endforeach
        </x-ui.select>
    </x-ui.field>

    <x-ui.field label="Position" for="sort_order" :error="$errors->first('sort_order')">
        <x-ui.input id="sort_order" name="sort_order" type="number" min="0"
                    value="{{ old('sort_order', $faq->sort_order ?? 0) }}" :invalid="$errors->has('sort_order')" />
    </x-ui.field>

    <x-ui.checkbox name="is_published" value="1" label="Published"
                   :checked="(bool) old('is_published', $faq->is_published ?? true)" />
</div>
