@php $module = $module ?? null; @endphp

<div x-data="{ unlockType: '{{ old('unlock_type', $module?->unlock_type?->value ?? 'immediate') }}' }" class="space-y-5">
    <x-ui.field label="Availability" for="unlock_type" :error="$errors->first('unlock_type')" required>
        <x-ui.select id="unlock_type" name="unlock_type" x-model="unlockType" :invalid="$errors->has('unlock_type')">
            @foreach (\App\Enums\ModuleUnlockType::options() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-ui.select>
    </x-ui.field>

    <div x-show="unlockType === 'days_after_enrollment'" x-cloak>
        <x-ui.field label="Days after enrollment" for="unlock_after_days" :error="$errors->first('unlock_after_days')">
            <x-ui.input id="unlock_after_days" name="unlock_after_days" type="number" min="1" max="3650"
                        value="{{ old('unlock_after_days', $module?->unlock_after_days) }}"
                        :invalid="$errors->has('unlock_after_days')" />
        </x-ui.field>
    </div>

    <div x-show="unlockType === 'fixed_date'" x-cloak>
        <x-ui.field label="Unlock date" for="unlock_at" :error="$errors->first('unlock_at')">
            <x-ui.input id="unlock_at" name="unlock_at" type="date"
                        value="{{ old('unlock_at', $module?->unlock_at?->toDateString()) }}"
                        :invalid="$errors->has('unlock_at')" />
        </x-ui.field>
    </div>
</div>
