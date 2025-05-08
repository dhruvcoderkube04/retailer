<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function ticketList()
    {
        $user_id = Auth::user()->id;
        $tickets = Ticket::where('user_id',$user_id)->get();
        return view('support.ticketlist', compact('tickets'));
    }

    public function updateTicketStatus(Request $request, $ticket_id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:Open,Pending,In Progress,Resolved,Closed',
        ]);

        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        $ticket->status = $request->status;
        $ticket->save();

        Log::info('Ticket status updated', [
            'ticket_id' => $ticket_id,
            'new_status' => $request->status,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully.',
            'status' => $ticket->status,
        ]);
    }

    public function generateTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'ticket_description' => 'required|string',
            'ticket_image_ref'   => 'nullable|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Genrate random tikcet id 10 digit  with  TM add tm in prefix
        $ticket_id = 'TM' . mt_rand(100000, 999999);

        // Create a new ticket
        $ticket = new Ticket;
        $ticket->subject = $request->subject;
        $ticket->description = $request->ticket_description;
        $ticket->status = 'Open';
        $ticket->category = '';
        $ticket->ticket_id = $ticket_id;

        $ticket->user_id = Auth::user()->id;

        if ($request->hasFile('ticket_image_ref')) {
            $file = $request->file('ticket_image_ref');
            $path = $file->storePublicly('tickets', 'spaces'); // 'tickets' is the folder in your Space

            // Save URL to DB or return to client
            $ticket->ref_image = Storage::disk('spaces')->url($path); // Store full URL
        }

        $ticket->save();

        // Return JSON response
        return response()->json(['success' => true, 'message' => 'Ticket Created Successfully', 'ticket' => $ticket]);
    }
}
