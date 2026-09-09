<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = SupportTicketStatus::tryFrom($request->string('status')->toString());

        $tickets = SupportTicket::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->with('user')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.support-tickets.index', [
            'tickets' => $tickets,
            'activeStatus' => $status,
        ]);
    }

    public function show(SupportTicket $ticket): View
    {
        return view('admin.support-tickets.show', [
            'ticket' => $ticket->load(['user', 'replies.user']),
        ]);
    }

    public function updateStatus(Request $request, SupportTicket $ticket, AdminActivityLogger $activity): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', new Enum(SupportTicketStatus::class)]]);

        $ticket->update(['status' => $validated['status']]);
        $activity->log('support_ticket.status_changed', $ticket, "Set support ticket #{$ticket->id} to {$ticket->status->label()}");

        return back()->with('status', 'Ticket updated.');
    }
}
