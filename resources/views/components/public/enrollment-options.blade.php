@props(['course', 'availableGateways'])

<div x-data="{ choosing: false }">
    <div x-show="!choosing">
        <x-ui.button type="button" size="lg" class="w-full" @click="choosing = true">
            Request enrollment
        </x-ui.button>
    </div>

    <div x-show="choosing" x-collapse x-cloak class="space-y-4">
        @if (count($availableGateways) > 0)
            <div class="space-y-2">
                <p class="text-[0.6875rem] font-medium tracking-[0.08em] text-ink-500 uppercase">Pay online</p>
                @foreach ($availableGateways as $gateway)
                    <form method="POST" action="{{ route('courses.enroll', $course) }}">
                        @csrf
                        <input type="hidden" name="gateway" value="{{ $gateway->identifier() }}">
                        <x-ui.button type="submit" size="lg" class="w-full">
                            Pay via {{ $gateway->label() }}
                        </x-ui.button>
                    </form>
                @endforeach
            </div>

            <div class="flex items-center gap-3 text-[0.6875rem] text-ink-400" aria-hidden="true">
                <span class="h-px flex-1 bg-ink-200"></span>
                OR
                <span class="h-px flex-1 bg-ink-200"></span>
            </div>
        @endif

        <div class="space-y-2">
            <p class="text-[0.6875rem] font-medium tracking-[0.08em] text-ink-500 uppercase">Have an invite code?</p>
            <form method="POST" action="{{ route('courses.enroll.invite', $course) }}" class="space-y-2">
                @csrf
                <x-ui.input name="code" value="{{ old('code') }}" placeholder="INVITE CODE"
                            class="uppercase" :invalid="$errors->has('code')" />
                @error('code')
                    <p class="text-[0.8125rem] text-red-600">{{ $message }}</p>
                @enderror
                <x-ui.button type="submit" variant="outline" size="lg" class="w-full">Redeem code</x-ui.button>
            </form>
        </div>

        @if (count($availableGateways) === 0)
            <div class="space-y-2 border-t border-ink-200 pt-4">
                <form method="POST" action="{{ route('courses.enroll', $course) }}">
                    @csrf
                    <x-ui.button type="submit" variant="ghost" size="sm" class="w-full">
                        No code? Request enrollment and pay another way
                    </x-ui.button>
                </form>
                <p class="text-center text-[0.8125rem] text-ink-500">
                    Our team will contact you to arrange payment and open the course.
                </p>
            </div>
        @endif

        <button type="button" class="text-[0.8125rem] text-ink-500 hover:underline" @click="choosing = false">
            Cancel
        </button>
    </div>
</div>
