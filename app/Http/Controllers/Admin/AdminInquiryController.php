<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inquiry;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminInquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%")
                    ->orWhere('message', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->get('status') === 'read') {
                $query->where('is_read', true);
            } elseif ($request->get('status') === 'unread') {
                $query->where('is_read', false);
            }
        }

        // CSV export
        if ($request->get('export') == 1) {
            $selected = $request->get('selected', null);

            $items = $query->when($selected && is_array($selected), function ($q) use ($selected) {
                return $q->whereIn('id', $selected);
            })->get();

            $response = new StreamedResponse(function () use ($items) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['id', 'name', 'email', 'subject', 'message', 'is_read', 'created_at']);

                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->name,
                        $item->email,
                        $item->subject,
                        $item->message,
                        $item->is_read ? 'read' : 'unread',
                        $item->created_at,
                    ]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="inquiries.csv"');

            return $response;
        }

        $inquiries = $query->paginate(20)->withQueryString();

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show($id)
    {
        $inquiry = Inquiry::with('user')->findOrFail($id);
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function update(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $data = $request->validate([
            'is_read' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:pending,resolved'],
        ]);

        if (array_key_exists('is_read', $data)) {
            $inquiry->is_read = (bool) $data['is_read'];
        }

        if (array_key_exists('status', $data)) {
            $inquiry->status = $data['status'];
        }

        $inquiry->save();

        return redirect()->back()->with('success', 'Inquiry updated.');
    }

    public function destroy($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted.');
    }
}
