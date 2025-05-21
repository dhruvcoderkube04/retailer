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

   public function FetchticketList(Request $request)
    {
        $user_id = Auth::id();
        $search = $request->input('search');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw = $request->input('draw');

        $query = Ticket::where('user_id', $user_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $total = Ticket::where('user_id', $user_id)->count();
        $filtered = $query->count();

        $tickets = $query->offset($start)
            ->limit($length)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($tickets as $ticket) {
            $statusClass = match (strtolower($ticket->status)) {
                'open' => 'badge badge-danger',
                'in progress' => 'badge badge-info',
                'resolved' => 'badge badge-success',
                'closed' => 'badge badge-secondary',
                default => 'badge badge-light',
            };

            $dropdown = '';
            if ($ticket->status == 'Closed') {
                $dropdown = '<select class="form-select form-select-sm ticket-status"
                                data-ticket-id="' . $ticket->ticket_id . '">
                                <option value="">Action</option>
                                <option value="Open">Open</option>
                            </select>';
            }

            $data[] = [
                'checkbox' => '<div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="' . $ticket->id . '" />
                            </div>',
                'ticket_id' => '<a href="#" class="text-gray-800 text-hover-primary mb-1">' . e($ticket->ticket_id) . '</a>',
                'subject' => e($ticket->subject),
                'description' => e($ticket->description),
                'ref_image' => '<img src="' . asset($ticket->ref_image) . '" width="50">',
                'status' => '<span class="' . $statusClass . '" data-status="' . $ticket->status . '">' . ucfirst($ticket->status) . '</span>',
                'created_at' => '<div class="badge badge-light">' . $ticket->created_at->diffForHumans() . '</div>',
                'actions' => $dropdown,
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function updateTicketStatus(Request $request, $ticket_id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
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
