@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Contact Admin</h1>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('inquiries.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm text-gray-700">Subject (optional)</label>
                <input name="subject" value="{{ old('subject') }}" class="w-full mt-1 px-3 py-2 border rounded" />
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-700">Message</label>
                <textarea name="message" rows="6" class="w-full mt-1 px-3 py-2 border rounded">{{ old('message') }}</textarea>
            </div>

            <div>
                <button class="px-4 py-2 bg-green-800 text-white rounded">Send Message</button>
            </div>
        </form>
    </div>
</div>
@endsection
