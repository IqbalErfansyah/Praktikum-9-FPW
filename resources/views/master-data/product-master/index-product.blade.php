<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>


    <div class="container mx-auto p-4">
        <div class="overflow-x-auto">
            @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-500">
                {{ session('success') }}
            </div>
            @elseif (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-500">
                {{ session('error') }}
            </div>
            @endif

            <form method="GET" action="{{ route('product-index') }}" class="mb-4 flex items-center">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari produk..." class="w-1/4 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">

                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}">

                <button type="submit" class="ml-2 rounded-lg bg-green-500 px-4 py-2 text-white shadow-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">Cari</button>
            </form>

            <a href="{{ route('product-create') }}"></a>
            <table class="min-w-full border-collapse border border-gray-200">
            </table>
        </div>
    </div>

    <div class="container p-4 mx-auto">
        <div class="overflow-x-auto">
            <a href="{{ route('product-create') }}">
                <button class="px-6 py-4 text-white bg-green-500 border border-green-500 rounded-lg shadow-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Add product data
                </button>
            </a>


            <table class="min-w-full border border-collapse border-gray-200">

                <thead>
                    <tr class="bg-gray-100">
                        @php
                        $columns = [
                        'id' => 'ID',
                        'product_name' => 'Product Name',
                        'unit' => 'Unit',
                        'type' => 'Type',
                        'information' => 'Information',
                        'qty' => 'Qty',
                        'producer' => 'Producer',
                        ];

                        $currentSortBy = request('sort_by', 'id');
                        $currentDirection = request('sort_direction', 'asc');
                        @endphp

                        @foreach ($columns as $column => $label)
                        @php
                        $newDirection = ($currentSortBy == $column && $currentDirection == 'asc') ? 'desc' : 'asc';
                        $arrow = ($currentSortBy == $column)
                        ? ($currentDirection == 'asc' ? ' ↑' : ' ↓')
                        : '';
                        $queryString = http_build_query(array_merge(
                        request()->except(['sort_by', 'sort_direction', 'page']),
                        ['sort_by' => $column, 'sort_direction' => $newDirection]
                        ));
                        @endphp

                        <th class="px-4 py-2 text-left text-gray-600 border border-gray-200">
                            <a href="?{{ $queryString }}" class="flex items-center hover:text-green-500">
                                {{ $label }} {!! $arrow !!}
                            </a>
                        </th>
                        @endforeach

                        <th class="px-4 py-2 text-left text-gray-600 border border-gray-200">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                    <tr class="bg-white">
                        <td class="px-4 py-2 border border-gray-200">{{ $item->id }}</td>
                        <td class="px-4 py-2 border border-gray-200">
                            <a href="{{ route('product-detail', $item->id) }}">
                                {{ $item->product_name }}
                            </a>
                        </td>
                        <td class="px-4 py-2 border border-gray-200">{{ $item->unit }}</td>
                        <td class="px-4 py-2 border border-gray-200">{{ $item->type }}</td>
                        <td class="px-4 py-2 border border-gray-200">{{ $item->information }}</td>
                        <td class="px-4 py-2 border border-gray-200">{{ $item->qty }}</td>
                        <td class="px-4 py-2 border border-gray-200">{{ $item->producer }}</td>
                        <td class="px-4 py-2 border border-gray-200">
                            <a href="{{ route('product-edit', $item->id) }}"
                                class="px-2 text-blue-600 hover:text-blue-800">Edit</a>
                            <button class="px-2 text-red-600 hover:text-red-800"
                                onclick="confirmDelete('<? route('product-destroy', $item->id) ?>')">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <p class="mb-4 text-center text-2xl font-bold text-red-600"> No products found </p>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $data->appends(["search" => request("search")])->links()}}
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(deleteUrl) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini ? ')) {
                // Jika user mengonfirmasi, kita dapat membuat form dan mengirimkan permintaan delete
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                // Tambahkan CSRF token
                let csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                // Tambahkan method spoofing untuk DELETE (karena HTML form hanya mendukung GET dan POST) 
                let methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                // Tambahkan form ke body dan submit
                document.body.appendChild(form);
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const showAlert = (message, type = 'success') => {
                // Buat overlay semi-transparan
                const overlay = document.createElement('div');
                overlay.className = `
            fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50
        `;

                // Buat box alert
                const alertBox = document.createElement('div');
                alertBox.className = `
            bg-white rounded-lg shadow-lg p-6 text-center max-w-sm w-full
            ${type === 'success' ? 'border-t-4 border-green-500' : 'border-t-4 border-red-500'}
        `;

                // Pesan teks
                const text = document.createElement('p');
                text.textContent = message;
                text.className = 'text-gray-700 mb-4';

                // Tombol OK
                const button = document.createElement('button');
                button.textContent = 'OK';
                button.className = `
            px-4 py-2 rounded-lg text-white font-semibold
            ${type === 'success' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'}
        `;
                button.addEventListener('click', () => overlay.remove());

                // Gabungkan elemen
                alertBox.appendChild(text);
                alertBox.appendChild(button);
                overlay.appendChild(alertBox);
                document.body.appendChild(overlay);
            };

            // Panggil alert berdasarkan session
            @if(session('success'))
            showAlert(@json(session('success')), 'success');
            @endif

            @if(session('error'))
            showAlert(@json(session('error')), 'error');
            @endif
        });
    </script>

</x-app-layout>