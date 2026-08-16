<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    public function create()
    {
        return view('inquiry.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:50'],
            'target_role' => ['nullable', 'string', 'max:30'],
            'priority' => ['nullable', 'string', 'max:20'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        Inquiry::create([
            'user_id' => $user ? $user->id : null,
            'name' => $user ? $user->name : ($data['name'] ?? 'Guest'),
            'email' => $user ? $user->email : ($data['email'] ?? null),
            'subject' => $data['subject'] ?? null,
            'category' => $data['category'] ?? 'general',
            'target_role' => $data['target_role'] ?? 'admin',
            'priority' => $data['priority'] ?? 'normal',
            'message' => $data['message'],
            'is_read' => false,
            'status' => 'new',
        ]);

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Your message has been sent.');
        }

        return redirect()->route('home')->with('success', 'Your message has been sent.');
    }
}
