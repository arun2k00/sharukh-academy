<x-layouts.public title="Refund Policy" description="Sharukh Academy's no-refund policy for digital courses, and the limited exceptions we do honour.">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-[1400px] px-6 py-14 lg:px-12 lg:py-20">
            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Legal</p>
            <h1 class="mt-4 text-[2rem] leading-[1.15] sm:text-[2.5rem]">Refund Policy</h1>
            <p class="mt-4 text-[0.8125rem] text-ink-500">Last updated: {{ now()->format('j F Y') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-6 py-14 lg:px-12 lg:py-16">
        <div class="prose-academy">
            <p>
                This Refund Policy explains, in plain terms, how
                {{ $academySettings['academy_name'] ?? config('academy.name') }} handles refund requests. It forms
                part of our <a href="{{ route('legal.terms') }}">Terms and Conditions</a>.
            </p>

            <h2>1. Our policy: no refunds</h2>
            <p>
                All course purchases are final. Every course is a digital product: the moment your enrollment is
                confirmed, you are granted immediate, full access to its recorded lessons, live-class schedule, and
                downloadable resources. Because that access cannot be "returned" or revoked back into an unused
                state the way a physical product can, we do not offer refunds for a change of mind, lack of time to
                complete a course, dissatisfaction with your own progress, or any similar reason once an enrollment
                is confirmed.
            </p>
            <p>
                We choose to be upfront about this rather than bury it in fine print: please review a course's
                curriculum, prerequisites, instructor, and free preview lessons carefully, and ask us anything
                using the <a href="{{ route('contact') }}">contact page</a> before you enroll, so you can choose
                wisely.
            </p>

            <h2>2. The limited exceptions we do honour</h2>
            <p>
                A strict no-refund policy is about buyer's remorse, not about us keeping money we should not have
                taken in the first place. We will correct, refund, or credit a payment where:
            </p>
            <ul>
                <li><strong>Duplicate or erroneous charge</strong> — you were charged more than once for the same
                    enrollment, or charged an amount that does not match the price shown at checkout;</li>
                <li><strong>Unauthorised transaction</strong> — a payment was made from your account or card without
                    your authorisation, subject to our standard verification checks;</li>
                <li><strong>Failure to deliver</strong> — a technical fault on our side means you were charged but
                    never received access to the course you purchased, and we are unable to fix that access within
                    a reasonable time; or</li>
                <li><strong>Course cancelled by us</strong> — we cancel or permanently withdraw a course you
                    purchased before you have accessed a meaningful portion of it.</li>
            </ul>
            <p>
                These exceptions are assessed case by case and are not a general substitute for the no-refund
                policy in section 1 — for example, having watched a small preview portion of a course does not by
                itself disqualify an otherwise valid failure-to-deliver claim, but broad use of the course material
                is a strong signal that access was in fact delivered and used.
            </p>

            <h2>3. How to request a correction</h2>
            <p>
                Email <a href="mailto:{{ $academySettings['contact_email'] ?? config('academy.support_email') }}">{{ $academySettings['contact_email'] ?? config('academy.support_email') }}</a>
                within 7 days of the charge, with your order or enrollment ID, the email address used to purchase,
                and a short description of the issue. We aim to acknowledge every request within 2 business days
                and resolve valid requests within 10 business days. Where an exception applies, we will refund the
                original payment method or, where that is not possible, issue an account credit of equal value.
            </p>

            <h2>4. Chargebacks</h2>
            <p>
                If you believe a charge is incorrect, please contact us first — most issues are resolved faster
                this way than through your bank or card issuer. Filing a chargeback for a validly delivered
                enrollment, without first giving us the chance to respond, is treated as a violation of our
                <a href="{{ route('legal.terms') }}">Terms and Conditions</a> and may result in suspension of your
                account.
            </p>

            <h2>5. Live-class and cohort-based courses</h2>
            <p>
                Where a course is delivered as a scheduled, cohort-based live programme, the same no-refund policy
                applies once your seat is confirmed, since a seat and instructor time are reserved on your behalf
                regardless of your attendance. If we reschedule or cancel a live session, we will offer a makeup
                session or recording rather than a refund, except where section 2 applies.
            </p>

            <h2>6. Changes to this policy</h2>
            <p>
                We may update this Refund Policy from time to time. Any update applies to purchases made after the
                "Last updated" date above; it does not retroactively remove an exception you were entitled to at
                the time you paid.
            </p>

            <h2>7. Contact</h2>
            <p>
                Questions about this policy or an existing charge can be sent to
                <a href="mailto:{{ $academySettings['contact_email'] ?? config('academy.support_email') }}">{{ $academySettings['contact_email'] ?? config('academy.support_email') }}</a>{{ !empty($academySettings['contact_address']) ? ' or by post to '.$academySettings['contact_address'] : '' }}.
            </p>
        </div>
    </section>
</x-layouts.public>
