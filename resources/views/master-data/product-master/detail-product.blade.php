<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Product Detail') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <div class="overflow-x-auto rounded-lg bg-white p-6 shadow-md">

            <a href="{{ route('product-index') }}" class="text-blue-500 hover:underline">← Back</a>

            <div class="mt-4">
                <h3 class="mb-4 text-2xl font-semibold">{{ $product->product_name }}</h3>
                <p><strong>ID:</strong><strong> {{ $product->id }}</strong></p>
                <p><strong>Unit:</strong><strong> {{ $product->unit }}</strong></p>
                <p><strong>Type:</strong><strong> {{ $product->type }}</strong></p>
                <p><strong>Information:</strong><strong> {{ $product->information }}</strong></p>
                <p><strong>Quantity:</strong><strong> {{ $product->qty }}</strong></p>
                <p><strong>Producer:</strong><strong> {{ $product->producer }}</strong></p>
            </div>
        </div>
    </div>
</x-app-layout>