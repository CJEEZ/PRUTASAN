@extends('layouts.app')

@section('hideHeader')@endsection

@section('content')
<div class="flex w-full flex-col gap-6 p-3 sm:p-4 lg:flex-row lg:gap-6 lg:p-6" style="min-height:70vh;">
    <aside class="hidden lg:block w-64 rounded bg-white p-4 shadow">
        @include('seller._sidebar')
    </aside>
    <div class="lg:hidden w-full">
        @include('seller._mobile_nav')
    </div>
    <div class="flex-1">
        <div class="rounded bg-white p-4 shadow sm:p-6">
            <h2 class="mb-4 text-lg font-semibold sm:text-xl">Add Product</h2>
            <form method="POST" action="{{ route('seller.products.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium">Name *</label>
                    <input name="name" class="min-h-[40px] w-full rounded border border-gray-300 p-2" required>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Price *</label>
                        <input name="price" type="number" step="0.01" class="min-h-[40px] w-full rounded border border-gray-300 p-2" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Stock *</label>
                        <input name="stock" type="number" class="min-h-[40px] w-full rounded border border-gray-300 p-2" required>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Description</label>
                    <textarea name="description" class="min-h-[100px] w-full rounded border border-gray-300 p-2"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Category *</label>
                        <select name="category_id" class="min-h-[40px] w-full rounded border border-gray-300 p-2" required>
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Unit *</label>
                        <input name="unit" class="min-h-[40px] w-full rounded border border-gray-300 p-2" placeholder="e.g., kg, lb" required>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Image URL</label>
                    <input name="image_url" class="min-h-[40px] w-full rounded border border-gray-300 p-2" placeholder="https://example.com/image.jpg">
                </div>

                @if($isArindo)
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <h3 class="mb-3 font-semibold">Arindo Property Details</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="text-sm font-medium">Is Arindo Listing?</label>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="is_arindo" value="1" checked class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            <span class="text-sm text-gray-700">Enable Arindo property fields</span>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Loan Amount</label>
                            <input name="loan_amount" type="number" step="0.01" class="w-full border rounded p-2" placeholder="₱0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Term (years)</label>
                            <input name="term_years" type="number" class="w-full border rounded p-2" placeholder="3">
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Expiration Date</label>
                            <input name="expiration_date" type="date" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Location</label>
                            <input name="location" class="w-full border rounded p-2" placeholder="Laguna, Philippines">
                        </div>
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Map Pin / LatLong</label>
                            <input name="map_location" class="w-full border rounded p-2" placeholder="14.1422, 121.2908">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Soil Report URL</label>
                            <input name="soil_report_url" type="url" class="w-full border rounded p-2" placeholder="https://example.com/soil-report.pdf">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Crop Yield Description</label>
                        <textarea name="crop_yield_description" class="w-full border rounded p-2" placeholder="Produces 500kg of lanzones per season"></textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Legal Document URL</label>
                        <input name="legal_document_url" type="url" class="w-full border rounded p-2" placeholder="https://example.com/land-title.pdf">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Land Photo URLs (one per line)</label>
                        <textarea name="land_photo_urls" rows="4" class="w-full border rounded p-2" placeholder="https://example.com/photo1.jpg\nhttps://example.com/photo2.jpg"></textarea>
                    </div>
                </div>
                @endif

                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded">Add Product</button>
            </form>
        </div>
    </div>
</div>
@endsection
