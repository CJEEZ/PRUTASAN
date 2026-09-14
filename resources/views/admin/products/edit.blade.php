@extends('layouts.app')

@section('content')
<style>
    header { display: none; }
</style>

<div class="min-h-screen bg-gray-100">
    <div class="w-full mx-auto px-3 sm:px-6 lg:px-8 py-4">
        <div class="max-w-2xl">
            <div class="mb-4 flex items-center gap-3 text-sm">
                <a href="{{ route('admin.products.index') }}" class="text-orange-600 hover:text-orange-700 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Products
                </a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 inline-flex items-center">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
            </div>

            <div class="bg-white rounded-lg shadow p-3 sm:p-5">
                <h1 class="text-xl font-bold text-gray-900 mb-4">Edit Product</h1>

                @if ($errors->any())
                    <div class="mb-4 p-3 text-sm bg-red-100 text-red-800 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm">Product Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm">Description</label>
                        <textarea name="description" id="description" rows="4" required
                            class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2 sm:gap-4">
                        <div>
                            <label for="price" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm">Price (₱)</label>
                            <input type="number" name="price" id="price" step="0.01" value="{{ old('price', $product->price) }}" required
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="stock" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm">Stock</label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 sm:gap-4">
                        <div>
                            <label for="category_id" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm">Category</label>
                            <select name="category_id" id="category_id" required
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="unit" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm">Unit</label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit', $product->unit) }}" placeholder="e.g., kg, lb" required
                                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label for="image" class="block text-xs font-medium text-gray-700 mb-1 sm:text-sm">Product Image</label>
                        <div class="flex items-center gap-3">
                            @if ($product->image_url)
                                <img id="product-image-preview" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-16 w-16 object-cover rounded-lg ring-2 ring-orange-200">
                            @else
                                <img id="product-image-preview" src="" alt="Product image preview" class="hidden h-16 w-16 object-cover rounded-lg ring-2 ring-orange-200">
                            @endif
                            <label for="image" class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-orange-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-orange-700">
                                <i class="fas fa-camera"></i>
                                Replace image
                            </label>
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                        </div>
                        <p id="product-image-name" class="mt-1 text-xs text-gray-500">JPG, PNG, or WEBP, maximum 2MB</p>
                        <label for="image_url" class="mt-2 block text-xs font-medium text-gray-600">Or use an external image URL</label>
                        <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $product->image_url) }}"
                            class="mt-1 w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            placeholder="https://example.com/image.jpg">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-1.5 text-sm bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold">
                            Update Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="px-4 py-1.5 text-sm bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('image')?.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;

        document.getElementById('product-image-preview').src = URL.createObjectURL(file);
        document.getElementById('product-image-preview').classList.remove('hidden');
        document.getElementById('product-image-name').textContent = file.name;
    });
</script>
@endsection
