<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Rules\NoCodeInjection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class TicketController extends Controller
{
    public function ticketList()
    {
        $user_id = Auth::user()->id;
        $tickets = Ticket::where('user_id', $user_id)->get();
        return view('support.ticketlist', compact('tickets'));
    }

    public function createTicket()
    {
        return view('support.create-ticket');
    }

    public function FetchticketList(Request $request)
    {
        $user_id = Auth::id();
        $search = cleanInput($request->input('search'));
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw = $request->input('draw');

        $query = Ticket::where('user_id', $user_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if (!empty($search)) {
            $search = trim($search);
            $search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');


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

            $statusClass = match (strtolower($ticket->status)) {
                'open' => 'badge badge-danger',
                'in progress' => 'badge badge-info',
                'resolved' => 'badge badge-success',
                'closed' => 'badge badge-secondary',
                default => 'badge badge-light',
            };

            $dropdown = '<div class="d-flex align-items-center gap-2">';
            if ($ticket->status == 'Closed') {
                $dropdown = '<select class="form-select form-select-sm ticket-status"
                                data-ticket-id="' . encryptId($ticket->ticket_id) . '">
                                <option value="">Action</option>
                                <option value="Open">Open</option>
                            </select>';
            }
            $dropdown = '<a href="' . route('retailer.ticket.details', encryptId($ticket->ticket_id)) . '" class="btn btn-icon btn-success btn-light-success w-30px h-30px view-wholesaler" data-bs-toggle="tooltip" title="View">
                    <i class="ki-duotone ki-eye">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </a>
                </div>';
            $data[] = [
                'ticket_id' => '<a href="' . route('retailer.ticket.details', encryptId($ticket->ticket_id)) . '" class="text-gray-800 text-hover-primary mb-1">' . e($ticket->ticket_id) . '</a>',
                // 'ticket_id' => '<a href="" class="text-gray-800 text-hover-primary mb-1">' . e($ticket->ticket_id) . '</a>',
                'subject' => e($ticket->subject),
                'description' => e(Str::limit($ticket->description ?? '', 100)),
                'category' => e($ticket->category),
                'ref_image' => '<img src="' . ($firstImage
                    ? Storage::disk('spaces')->url($firstImage)
                    : asset('assets/media/images/no_image.jpg')) . '"
                    width="50"
                    onerror="this.onerror=null;this.src=\'' . asset('assets/media/images/no_image.jpg') . '\';">',
                'status' => '<span class="' . $statusClass . '" data-status="' . $ticket->status . '">' . ucfirst($ticket->status) . '</span>',
                'created_at' => '<div class="badge badge-light fs-7">' . $ticket->created_at->diffForHumans() . '</div>',
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
            // Validate inputs, if fails Laravel automatically returns 422 JSON for AJAX
            $validator = \Validator::make($request->all(), [
                'subject' => ['required', 'string', 'max:255', new NoCodeInjection],
                'category' => ['nullable'],
                'ticket_description' => ['required', 'string', 'max:255', new NoCodeInjection],
                'ticket_image_ref' => ['nullable', 'array', 'max:3'],
                'ticket_image_ref.*' => ['bail', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            ], [
                'ticket_image_ref.max' => 'You can upload up to 3 images only.',
                'ticket_image_ref.*.image' => 'Each file must be an image.',
                'ticket_image_ref.*.mimes' => 'Only JPEG and PNG images are allowed.',
                'ticket_image_ref.*.max' => 'Each image must be less than 2MB.',
            ]);

            if ($validator->fails()) {
                // Return JSON errors for AJAX
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Manually check your custom validations
            if ($request->input('subject') === $request->input('ticket_description')) {
                return response()->json([
                    'errors' => ['ticket_description' => ['Subject and Description must not be the same.']]
                ], 422);
            }

            $duplicateTicket = Ticket::where('subject', $request->input('subject'))
                ->where('description', $request->input('ticket_description'))
                ->first();

            if ($duplicateTicket) {
                return response()->json([
                    'errors' => ['subject' => ['A ticket with the same subject and description already exists.']]
                ], 422);
            }

            $ticket_id = 'TM' . mt_rand(100000, 999999);

            $ticket = new Ticket();
            $ticket->subject = sanitize_input($request->subject);
            $ticket->description = sanitize_input($request->ticket_description);
            $ticket->category = ($request->category ?? '') . '-' . ($request->product_id ?? '');
            $ticket->status = 'Open';
            $ticket->ticket_id = $ticket_id;
            $ticket->user_id = Auth::id();

            $filenames = [];

            if ($request->hasFile('ticket_image_ref')) {
                foreach ($request->file('ticket_image_ref') as $file) {
                    if (!@getimagesize($file->getPathname())) {
                        return $this->handleError(
                            $request,
                            ['ticket_image_ref' => ['One of the uploaded images is corrupted or unreadable.']],
                            422
                        );
                    }

                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('tickets', $filename, 'spaces');
                    $filenames[] = $filename;
                }
            }

            $ticket->ref_image = implode(',', $filenames);
            $ticket->save();

            return $this->handleSuccess($request, 'Ticket created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->handleError($request, $e->errors(), 422);

        } catch (\Exception $e) {
            return $this->handleError($request, ['general' => ['Something went wrong: ' . $e->getMessage()]], 500);
        }
    }

    public function ticketDetail(Request $request, $ticket_id)
    {
        $ticket_id = decryptId($ticket_id);
        $user_id = Auth::user()->id;
        $ticket = Ticket::where('user_id', $user_id)->where('ticket_id', $ticket_id)->first();
        return view('support.ticket-detail-show', compact('ticket'));
    }

    private function handleSuccess(Request $request, $message)
    {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ], 200);
        }

        return redirect()->route('wholesaler.ticket.list')->with('success', $message);
    }

    private function handleError(Request $request, $errors, $status = 422)
    {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'errors' => $errors
            ], $status);
        }

        return redirect()->back()->withErrors($errors)->withInput();
    }
}
