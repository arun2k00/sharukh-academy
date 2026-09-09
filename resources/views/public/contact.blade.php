<x-layouts.public title="Contact" description="Questions about a programme, corporate enrolment or anything else.">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-6xl px-5 py-14 lg:px-8 lg:py-20">
            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Contact</p>
            <h1 class="mt-5 max-w-2xl text-[2rem] leading-tight sm:text-[2.5rem]">Ask us anything before you enrol</h1>
            <p class="mt-5 max-w-xl text-[1.0625rem]/8 text-ink-600">
                We answer within one working day. If a programme is not right for you, we would rather tell you now.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-5 py-14 lg:px-8 lg:py-16">
        <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7">
                <x-ui.flash />

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-ui.field label="Your name" for="name" :error="$errors->first('name')" required>
                            <x-ui.input id="name" name="name" value="{{ old('name') }}" required
                                        autocomplete="name" :invalid="$errors->has('name')" />
                        </x-ui.field>

                        <x-ui.field label="Email address" for="email" :error="$errors->first('email')" required>
                            <x-ui.input id="email" name="email" type="email" value="{{ old('email') }}" required
                                        autocomplete="email" :invalid="$errors->has('email')" />
                        </x-ui.field>
                    </div>

                    <x-ui.field label="Subject" for="subject" :error="$errors->first('subject')">
                        <x-ui.input id="subject" name="subject" value="{{ old('subject') }}"
                                    :invalid="$errors->has('subject')" />
                    </x-ui.field>

                    <x-ui.field label="Message" for="message" :error="$errors->first('message')"
                                hint="Tell us what you are working on — it helps us answer usefully." required>
                        <x-ui.textarea id="message" name="message" rows="7" required
                                       :invalid="$errors->has('message')">{{ old('message') }}</x-ui.textarea>
                    </x-ui.field>

                    <x-ui.button type="submit" size="lg">Send message</x-ui.button>
                </form>
            </div>

            <div class="lg:col-span-5">
                <div class="border-t border-ink-900 pt-6">
                    <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Direct</h2>
                    <ul class="mt-5 space-y-4 text-sm">
                        <li class="flex gap-3">
                            <x-ui.icon name="mail" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                            <a href="mailto:{{ $settings['contact_email'] ?? config('academy.support_email') }}"
                               class="text-ink-800 hover:text-pine-700">
                                {{ $settings['contact_email'] ?? config('academy.support_email') }}
                            </a>
                        </li>
                        <li class="flex gap-3">
                            <x-ui.icon name="phone" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                            <span class="text-ink-800">{{ $settings['contact_phone'] ?? config('academy.support_phone') }}</span>
                        </li>
                        @if (! empty($settings['contact_address']))
                            <li class="flex gap-3">
                                <x-ui.icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                                <span class="text-ink-700">{{ $settings['contact_address'] }}</span>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="mt-10 border-t border-ink-200 pt-6">
                    <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Already a student?</h2>
                    <p class="mt-4 text-sm/6 text-ink-600">
                        Questions about a course you are taking are answered fastest in the live sessions, or by replying
                        to any email from your instructor.
                    </p>
                    <x-ui.button :href="route('login')" variant="outline" size="sm" class="mt-5">Sign in</x-ui.button>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
