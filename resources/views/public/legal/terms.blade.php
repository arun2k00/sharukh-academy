<x-layouts.public title="Terms and Conditions" description="The terms that govern use of Sharukh Academy's website and courses.">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-[1400px] px-6 py-14 lg:px-12 lg:py-20">
            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Legal</p>
            <h1 class="mt-4 text-[2rem] leading-[1.15] sm:text-[2.5rem]">Terms and Conditions</h1>
            <p class="mt-4 text-[0.8125rem] text-ink-500">Last updated: {{ now()->format('j F Y') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-6 py-14 lg:px-12 lg:py-16">
        <div class="prose-academy">
            <p>
                These Terms and Conditions ("Terms") are a legal agreement between you ("you", "student", "user")
                and {{ $academySettings['academy_name'] ?? config('academy.name') }} ("Sharukh Academy", "we", "us", "our"),
                governing your access to and use of our website, courses, live classes and any related services
                (together, the "Services"). By creating an account, enrolling in a course, or otherwise using the
                Services, you agree to be bound by these Terms. If you do not agree, please do not use the Services.
            </p>

            <h2>1. Eligibility and accounts</h2>
            <p>
                You must be at least 18 years old, or be using the Services under the supervision of a parent or
                legal guardian who agrees to these Terms on your behalf, to create an account. You are responsible
                for maintaining the confidentiality of your account credentials and for all activity that occurs
                under your account. Notify us immediately at {{ $academySettings['contact_email'] ?? config('academy.support_email') }}
                if you suspect unauthorised use of your account.
            </p>
            <p>
                You agree to provide accurate, current information when registering and to keep it up to date. We
                may suspend or terminate an account that we reasonably believe contains false information or has
                been used to violate these Terms.
            </p>

            <h2>2. Courses, access and licence</h2>
            <p>
                When you enroll in a course, we grant you a limited, non-exclusive, non-transferable, revocable
                licence to access that course's recorded lessons, live classes and downloadable resources for your
                own personal, non-commercial, educational use, for as long as your enrollment remains active under
                the access terms shown on that course's page (lifetime, a fixed duration, or a fixed expiry date).
                This licence does not transfer ownership of any content to you.
            </p>
            <p>You agree not to, and not to assist anyone else to:</p>
            <ul>
                <li>share your account or course access with anyone who has not separately enrolled;</li>
                <li>copy, record, reproduce, republish, download (beyond a resource explicitly marked
                    downloadable), sell, rent, sublicense or redistribute any course content;</li>
                <li>remove, obscure or alter any copyright, trademark or other proprietary notice; or</li>
                <li>reverse engineer, scrape or use automated means to access the Services beyond normal browser
                    use.</li>
            </ul>
            <p>
                We may suspend or terminate access, without a refund, for a violation of this section.
            </p>

            <h2>3. Payment and pricing</h2>
            <p>
                Prices are shown in Indian Rupees (₹) and are inclusive of applicable taxes unless stated otherwise.
                We may change course prices at any time; a change does not affect an enrollment you have already
                paid for. Depending on how a course is configured, payment may be processed immediately online, or
                recorded as a pending order that our team confirms manually — the course page and your order status
                will indicate which applies. All fees are due in full before access is granted, except where we
                have separately agreed instalments in writing.
            </p>
            <p>
                All sales are final. Please see our <a href="{{ route('legal.refunds') }}">Refund Policy</a> for the
                limited exceptions we do honour, such as a duplicate or erroneous charge.
            </p>

            <h2>4. Live classes</h2>
            <p>
                Live classes are conducted over Zoom or a similar platform at the scheduled time shown on your
                dashboard. Join links are personal to your enrollment and must not be shared. We may record a live
                class and make the recording available to enrolled students; by attending, you consent to being
                included in such a recording. We may reschedule, substitute the instructor for, or cancel a live
                class where necessary, and will make reasonable efforts to notify enrolled students in advance.
                Attendance at live classes is not a condition of completing a course unless a specific course's
                completion rules say otherwise.
            </p>

            <h2>5. Certificates</h2>
            <p>
                A certificate of completion is issued automatically once you meet the completion criteria stated on
                a course's page, and can be verified by anyone using the public verification link printed on it. A
                certificate confirms that you completed the stated curriculum; it is not a professional
                qualification, accreditation, or guarantee of employment, promotion, or any other outcome, and we
                make no representation that any employer will recognise or give weight to it.
            </p>

            <h2>6. Intellectual property</h2>
            <p>
                All course content — including video, text, graphics, worksheets, assessments, our logo, and the
                design of the Services — is owned by Sharukh Academy or its instructors and licensors and is protected by
                copyright and other intellectual property laws. Nothing in these Terms transfers any such
                intellectual property to you, except the limited licence described in section 2.
            </p>

            <h2>7. Acceptable use</h2>
            <p>You agree not to use the Services to:</p>
            <ul>
                <li>violate any applicable law or regulation;</li>
                <li>harass, abuse, or harm another student, instructor, or member of our team, including in
                    course Q&A, live classes, or reviews;</li>
                <li>upload or transmit any virus, malware, or other harmful code; or</li>
                <li>post content that is unlawful, defamatory, or infringes another person's rights.</li>
            </ul>
            <p>
                We may remove content or restrict access for a student who violates this section, at our
                reasonable discretion.
            </p>

            <h2>8. Reviews and community content</h2>
            <p>
                If you submit a course review, a question, a reply, or any other content through the Services, you
                grant us a worldwide, royalty-free, non-exclusive licence to display, reproduce and moderate that
                content in connection with operating the Services. We may edit, hide, or remove any user-submitted
                content, including a review, at our discretion — for example where it is abusive, off-topic, or
                appears fraudulent.
            </p>

            <h2>9. Disclaimers</h2>
            <p>
                The Services are provided "as is" and "as available". To the fullest extent permitted by law, we
                disclaim all warranties, express or implied, including any warranty of merchantability, fitness for
                a particular purpose, or non-infringement. We do not warrant that the Services will be
                uninterrupted, error-free, or free of harmful components, or that completing a course will result
                in any particular skill level, certification recognition, employment, or income outcome.
            </p>

            <h2>10. Limitation of liability</h2>
            <p>
                To the fullest extent permitted by law, Sharukh Academy and its instructors, officers, and employees will
                not be liable for any indirect, incidental, special, consequential, or punitive damages, or any
                loss of profits, revenue, data, or goodwill, arising from your use of the Services. Our total
                aggregate liability for any claim arising from these Terms or the Services is limited to the amount
                you paid us for the course giving rise to the claim in the twelve months before the claim arose.
                Nothing in these Terms limits a liability that cannot lawfully be limited or excluded.
            </p>

            <h2>11. Indemnification</h2>
            <p>
                You agree to indemnify and hold Sharukh Academy harmless from any claim, loss, or expense (including
                reasonable legal fees) arising from your violation of these Terms or misuse of the Services.
            </p>

            <h2>12. Changes to these Terms</h2>
            <p>
                We may update these Terms from time to time. If we make a material change, we will update the
                "Last updated" date above and, where appropriate, notify you by email or an in-app notice.
                Continued use of the Services after a change takes effect constitutes acceptance of the revised
                Terms.
            </p>

            <h2>13. Termination</h2>
            <p>
                You may stop using the Services at any time. We may suspend or terminate your access for a breach
                of these Terms, or where required by law, without a refund of fees already paid, except as set out
                in our Refund Policy.
            </p>

            <h2>14. Governing law</h2>
            <p>
                These Terms are governed by the laws of India, without regard to conflict-of-law principles. Any
                dispute arising from these Terms or the Services will be subject to the exclusive jurisdiction of
                the courts of Bengaluru, Karnataka.
            </p>

            <h2>15. Contact</h2>
            <p>
                Questions about these Terms can be sent to
                <a href="mailto:{{ $academySettings['contact_email'] ?? config('academy.support_email') }}">{{ $academySettings['contact_email'] ?? config('academy.support_email') }}</a>{{ !empty($academySettings['contact_address']) ? ' or by post to '.$academySettings['contact_address'] : '' }}.
            </p>
        </div>
    </section>
</x-layouts.public>
