<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Lightweight student support desk (§25): submit, track status, reply.
 */
class SupportTicketController extends Controller
{
    /**
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'general' => 'General question',
        'billing' => 'Billing and enrolment',
        'technical' => 'Technical issue',
        'course_content' => 'Course content',
    ];

    public function index(Request $request): View
    {
        return view('student.support.index', [
            'tickets' => $request->user()->supportTickets()->latest('id')->get(),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function create(): View
    {
        return view('student.support.create', ['categories' => self::CATEGORIES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(self::CATEGORIES))],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $ticket = $request->user()->supportTickets()->create($validated);

        return redirect()->route('support.show', $ticket)
            ->with('status', 'Your request has been submitted. We will reply here.');
    }

    public function show(Request $request, SupportTicket $ticket): View
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        return view('student.support.show', [
            'ticket' => $ticket->load('replies.user'),
        ]);
    }
}
