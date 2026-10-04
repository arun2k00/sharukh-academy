<x-layouts.tutor :title="'Invite codes — '.$course->title" heading="Invite codes">
    <x-admin.page-header :title="$course->title" :back="route('tutor.courses.edit', $course)" back-label="Course settings"
                         description="Share a code with a student to give them free access to this course, bypassing payment." />

    @if ($inviteCodes->isEmpty())
        <x-ui.empty-state icon="tag" title="No invite codes yet"
                          description="Generate one below and share it with the student who should redeem it." />
    @else
        <x-admin.table :headings="['Code', 'Uses', 'Expires', 'Status', '']">
            @foreach ($inviteCodes as $inviteCode)
                <tr>
                    <td class="px-4 py-3 font-mono text-[0.8125rem] text-ink-900">{{ $inviteCode->code }}</td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">
                        {{ $inviteCode->used_count }} / {{ $inviteCode->max_uses ?? 'unlimited' }}
                    </td>
                    <td class="px-4 py-3 text-ink-600">{{ $inviteCode->expires_at?->format('j M Y') ?? 'Never' }}</td>
                    <td class="px-4 py-3">
                        @if ($inviteCode->isRevoked())
                            <x-ui.badge class="bg-ink-100 text-ink-500">Revoked</x-ui.badge>
                        @elseif ($inviteCode->isExpired())
                            <x-ui.badge class="bg-sand-200 text-ink-600">Expired</x-ui.badge>
                        @elseif ($inviteCode->isExhausted())
                            <x-ui.badge class="bg-sand-200 text-ink-600">Exhausted</x-ui.badge>
                        @else
                            <x-ui.badge class="bg-pine-50 text-pine-700">Active</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if ($inviteCode->isRedeemable())
                            <x-admin.delete-form :action="route('tutor.invite-codes.destroy', $inviteCode)"
                                                 confirm="Revoke this invite code? It will no longer be redeemable."
                                                 label="Revoke" />
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
    @endif

    <x-ui.panel title="Generate a code" class="mt-8">
        <form method="POST" action="{{ route('tutor.courses.invite-codes.store', $course) }}" class="space-y-5">
            @csrf

            <x-ui.field label="Maximum uses" for="max_uses" :error="$errors->first('max_uses')"
                        hint="Leave blank for unlimited uses.">
                <x-ui.input id="max_uses" name="max_uses" type="number" min="1" value="{{ old('max_uses') }}"
                            :invalid="$errors->has('max_uses')" />
            </x-ui.field>

            <x-ui.field label="Expires on" for="expires_at" :error="$errors->first('expires_at')"
                        hint="Leave blank for a code that never expires.">
                <x-ui.input id="expires_at" name="expires_at" type="date" value="{{ old('expires_at') }}"
                            :invalid="$errors->has('expires_at')" />
            </x-ui.field>

            <x-ui.button type="submit" icon="plus">Generate code</x-ui.button>
        </form>
    </x-ui.panel>
</x-layouts.tutor>
