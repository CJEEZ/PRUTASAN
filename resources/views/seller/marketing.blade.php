@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full p-6 gap-6" style="min-height:70vh;">
    <aside class="hidden md:block w-64 bg-white rounded shadow p-4">
        @include('seller._sidebar')
    </aside>
    <div class="md:hidden w-full">
        @include('seller._mobile_nav')
    </div>
    <div class="flex-1">
        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Marketing Centre</h2>
            <p class="text-gray-600">Promotions and marketing tools will be available here.</p>
        </div>
    </div>
</div>
@endsection
