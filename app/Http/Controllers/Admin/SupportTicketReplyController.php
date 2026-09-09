<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketReplyController extends Controller
{
    public function __invoke(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $validated = $request->validate(['message' => ['required', 'string', 'max:4000']]);

        $ticket->replies()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        if ($ticket->status === SupportTicketStatus::Open) {
            $ticket->update(['status' => SupportTicketStatus::InProgress]);
        }

        return back()->with('status', 'Reply sent.');
    }
}
