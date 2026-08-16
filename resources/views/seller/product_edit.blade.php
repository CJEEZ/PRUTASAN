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
            <h2 class="text-xl font-semibold mb-4">Edit Product</h2>
            <form method="POST" action="{{ route('seller.products.update', $product->id) }}">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input name="name" value="{{ old('name', $product->name) }}" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Price</label>
                    <input name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Stock</label>
                    <input name="stock" type="number" value="{{ old('stock', $product->stock) }}" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" class="w-full border rounded p-2">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select name="category_id" class="w-full border rounded p-2" required>
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Unit</label>
                    <input name="unit" value="{{ old('unit', $product->unit) }}" class="w-full border rounded p-2" placeholder="e.g., kg, lb" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Image URL</label>
                    <input name="image_url" value="{{ old('image_url', $product->image_url) }}" class="w-full border rounded p-2" placeholder="https://example.com/image.jpg">
                    @if ($product->image_url)
                        <div class="mt-3">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-24 w-24 object-cover rounded-lg">
                        </div>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 p-4 mb-4 bg-gray-50">
                    <h3 class="font-semibold mb-3">Arindo Property Details</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm font-medium">Is Arindo Listing?</label>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="is_arindo" value="1" class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" {{ old('is_arindo', $product->is_arindo) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Enable Arindo property fields</span>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Loan Amount</label>
                            <input name="loan_amount" type="number" step="0.01" value="{{ old('loan_amount', $product->loan_amount) }}" class="w-full border rounded p-2" placeholder="₱0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Term (years)</label>
                            <input name="term_years" type="number" value="{{ old('term_years', $product->term_years) }}" class="w-full border rounded p-2" placeholder="3">
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 mt-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Expiration Date</label>
                            <input name="expiration_date" type="date" value="{{ old('expiration_date', optional($product->expiration_date)->format('Y-m-d')) }}" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Location</label>
                            <input name="location" value="{{ old('location', $product->location) }}" class="w-full border rounded p-2" placeholder="Laguna, Philippines">
                        </div>
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Map Pin / LatLong</label>
                            <input name="map_location" value="{{ old('map_location', $product->map_location) }}" class="w-full border rounded p-2" placeholder="14.1422, 121.2908">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Soil Report URL</label>
                            <input name="soil_report_url" type="url" value="{{ old('soil_report_url', $product->soil_report_url) }}" class="w-full border rounded p-2" placeholder="https://example.com/soil-report.pdf">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Crop Yield Description</label>
                        <textarea name="crop_yield_description" class="w-full border rounded p-2" placeholder="Produces 500kg of lanzones per season">{{ old('crop_yield_description', $product->crop_yield_description) }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Legal Document URL</label>
                        <input name="legal_document_url" type="url" value="{{ old('legal_document_url', $product->legal_document_url) }}" class="w-full border rounded p-2" placeholder="https://example.com/land-title.pdf">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Land Photo URLs (one per line)</label>
                        <textarea name="land_photo_urls" rows="4" class="w-full border rounded p-2" placeholder="https://example.com/photo1.jpg\nhttps://example.com/photo2.jpg">{{ old('land_photo_urls', is_array($product->land_photo_urls) ? implode("\n", $product->land_photo_urls) : '') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded">Update Product</button>
            </form>
        </div>
    </div>
</div>
@endsection
