<?php

namespace App\Http\Controllers\Student;

use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportTicketReplyController extends Controller
{
    public function __invoke(Request $request, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        // A student replying to a resolved ticket reopens it.
        if ($ticket->status === SupportTicketStatus::Resolved) {
            $ticket->update(['status' => SupportTicketStatus::Open]);
        }

        return back()->with('status', 'Reply sent.');
    }
}
