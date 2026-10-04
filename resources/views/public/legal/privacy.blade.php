<x-layouts.public title="Privacy Policy" description="How Sharukh Academy collects, uses and protects your personal data.">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-[1400px] px-6 py-14 lg:px-12 lg:py-20">
            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Legal</p>
            <h1 class="mt-4 text-[2rem] leading-[1.15] sm:text-[2.5rem]">Privacy Policy</h1>
            <p class="mt-4 text-[0.8125rem] text-ink-500">Last updated: {{ now()->format('j F Y') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-6 py-14 lg:px-12 lg:py-16">
        <div class="prose-academy">
            <p>
                This Privacy Policy explains how {{ $academySettings['academy_name'] ?? config('academy.name') }}
                ("Sharukh Academy", "we", "us") collects, uses, discloses and protects personal data when you visit our
                website or enroll in and use our courses (the "Services"). It is written to be read by a student, not
                a lawyer — if anything here is unclear, write to us using the contact details at the end.
            </p>

            <h2>1. Information we collect</h2>
            <h3>Information you give us directly</h3>
            <ul>
                <li><strong>Account information:</strong> name, email address and password when you register.</li>
                <li><strong>Profile information:</strong> anything you choose to add to your profile.</li>
                <li><strong>Payment information:</strong> when you pay for a course online, your card or payment
                    details are collected and processed directly by our payment processor — we do not store your
                    full card number on our own servers.</li>
                <li><strong>Content you submit:</strong> lesson notes, bookmarks, course questions and replies,
                    reviews, support tickets, and contact-form messages.</li>
            </ul>
            <h3>Information collected automatically</h3>
            <ul>
                <li><strong>Usage data:</strong> which lessons you have watched or completed, your progress through
                    a course, and when you last accessed it — this is what powers your dashboard and certificate
                    eligibility.</li>
                <li><strong>Device and log data:</strong> IP address, browser type, and pages visited, collected
                    automatically by our servers for security and diagnostic purposes.</li>
                <li><strong>Cookies:</strong> small files used to keep you signed in and remember your preferences.
                    See section 6.</li>
            </ul>

            <h2>2. How we use your information</h2>
            <p>We use personal data to:</p>
            <ul>
                <li>create and administer your account and enrollments;</li>
                <li>deliver course content, track your progress, and issue certificates;</li>
                <li>process payments and send order and enrollment confirmations;</li>
                <li>send service emails — enrollment confirmations, live-class reminders, replies to your
                    questions — and, where you have not opted out, occasional updates about new courses;</li>
                <li>respond to support requests and contact-form messages;</li>
                <li>maintain the security of the Services and prevent fraud or abuse; and</li>
                <li>understand, in aggregate, how our courses are used, so we can improve them.</li>
            </ul>

            <h2>3. How we share your information</h2>
            <p>We do not sell your personal data. We share it only with:</p>
            <ul>
                <li><strong>Payment processors</strong>, to process a payment you initiate;</li>
                <li><strong>Email and infrastructure providers</strong>, who send our emails and host our
                    application, acting under contract and only for that purpose;</li>
                <li><strong>Your instructor</strong>, who can see your name, questions and progress within a
                    course they teach, so they can answer your questions properly; and</li>
                <li><strong>Law enforcement or a regulator</strong>, only where we are legally required to.</li>
            </ul>
            <p>
                A course review you choose to publish is shown publicly with your name, as is any content on a
                certificate you choose to share.
            </p>

            <h2>4. Data retention</h2>
            <p>
                We keep your account and course data for as long as your account is active, so your progress and
                certificates remain available to you. If you ask us to delete your account, we will delete or
                anonymise your personal data within a reasonable period, except where we are required to keep a
                record (for example, a payment or tax record) for longer under law.
            </p>

            <h2>5. Your rights</h2>
            <p>Depending on where you live, you may have the right to:</p>
            <ul>
                <li>access the personal data we hold about you;</li>
                <li>correct inaccurate data (much of this you can already do from your profile);</li>
                <li>request deletion of your account and associated personal data;</li>
                <li>object to or restrict certain processing; and</li>
                <li>receive a copy of your data in a portable format.</li>
            </ul>
            <p>
                To exercise any of these rights, email
                <a href="mailto:{{ $academySettings['contact_email'] ?? config('academy.support_email') }}">{{ $academySettings['contact_email'] ?? config('academy.support_email') }}</a>.
                We will respond within a reasonable time and may need to verify your identity first.
            </p>

            <h2>6. Cookies</h2>
            <p>
                We use strictly necessary cookies to keep you signed in and to protect the Services from forgery
                attacks (CSRF). These are essential to the Services working and cannot be switched off. We do not
                currently use third-party advertising or cross-site tracking cookies.
            </p>

            <h2>7. Data security</h2>
            <p>
                We use reasonable technical and organisational measures — including encrypted connections (HTTPS),
                hashed passwords, and access controls on protected course resources — to protect your personal
                data. No method of transmission or storage is completely secure, and we cannot guarantee absolute
                security.
            </p>

            <h2>8. Children's privacy</h2>
            <p>
                The Services are not directed at children under 18. We do not knowingly collect personal data from
                a child under 18 without the consent of a parent or guardian. If you believe a child has provided
                us with personal data without such consent, contact us and we will delete it.
            </p>

            <h2>9. International visitors</h2>
            <p>
                Our servers and support team are based in India. If you access the Services from outside India,
                your data will be processed in India, where data protection laws may differ from those of your own
                country.
            </p>

            <h2>10. Changes to this policy</h2>
            <p>
                We may update this Privacy Policy from time to time. We will update the "Last updated" date above,
                and for a material change, will make reasonable efforts to notify you by email or an in-app notice.
            </p>

            <h2>11. Contact</h2>
            <p>
                For any question about this Privacy Policy or your personal data, contact us at
                <a href="mailto:{{ $academySettings['contact_email'] ?? config('academy.support_email') }}">{{ $academySettings['contact_email'] ?? config('academy.support_email') }}</a>{{ !empty($academySettings['contact_address']) ? ' or by post to '.$academySettings['contact_address'] : '' }}.
            </p>
        </div>
    </section>
</x-layouts.public>
