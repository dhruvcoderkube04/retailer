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

    public function createTicket()
    {
        return view('support.create-ticket');
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
            $images = explode(',', $ticket->ref_image);
            $firstImage = $images[0] ?? null;  // splits into array
            if ($firstImage) {
                $imageUrl = 'https://techsell.blr1.cdn.digitaloceanspaces.com/tickets/' . $firstImage;
            } else {
                $imageUrl = null; // or some placeholder image URL
            }

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
                                data-ticket-id="' . encryptId($ticket->ticket_id) . '">
                                <option value="">Action</option>
                                <option value="Open">Open</option>
                            </select>';
            }

            $data[] = [
                'ticket_id' => '<a href="' . route('retailer.ticket.details', encryptId($ticket->ticket_id)) . '" class="text-gray-800 text-hover-primary mb-1">' . e($ticket->ticket_id) . '</a>',
                // 'ticket_id' => '<a href="" class="text-gray-800 text-hover-primary mb-1">' . e($ticket->ticket_id) . '</a>',
                'subject' => e($ticket->subject),
                'description' => e($ticket->description),
                'ref_image' => '<img src="' . asset($imageUrl) . '" width="50">',
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
        $ticket_id = decryptId($ticket_id);
        // Validate the request
        $request->validate([
            'status' => 'required|in:Open',
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
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'ticket_description' => 'required|string',
                'ticket_image_ref' => 'nullable|array|max:3', // max 3 images
                'ticket_image_ref.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // 2MB max
            ]);

            $ticket_id = 'TM' . mt_rand(100000, 999999);

            $ticket = new Ticket();
            $ticket->subject = $request->subject;
            $ticket->description = $request->ticket_description;
            $ticket->status = 'Open';
            $ticket->category = '';
            $ticket->ticket_id = $ticket_id;
            $ticket->user_id = Auth::id();

            $filenames = [];

            if ($request->hasFile('ticket_image_ref')) {
                foreach ($request->file('ticket_image_ref') as $file) {
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('tickets', $filename, 'spaces');
                    $filenames[] = $filename;
                }
            }

            $ticket->ref_image = implode(',', $filenames);
            $ticket->save();

            return redirect()->back()->with('success', 'Ticket Created Successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log the error if needed: Log::error($e);
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    public function ticketDetail(Request $request,$ticket_id)
    {
        $ticket_id = decryptId($ticket_id);
        $user_id = Auth::user()->id;
        $ticket = Ticket::where('user_id',$user_id)->where('ticket_id',$ticket_id)->first();
        return view('support.ticket-detail-show',compact('ticket'));
    }
}
