<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MADO POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold">MADO POS</h1>
            </div>
            <nav class="mt-8">
                <a href="{{ route('dashboard') }}" class="block px-6 py-3 hover:bg-gray-800">DashboardD</a>
                <a href="{{ route('products.index') }}" class="block px-6 py-3 hover:bg-gray-800">Products</a>
                <a href="{{ route('raw-materials.index') }}" class="block px-6 py-3 hover:bg-gray-800">Raw Materials</a>
                <div class="px-6 py-3 text-sm text-gray-400">Stock Management</div>
                <a href="{{ route('stock-inputs.index') }}" class="block px-6 py-3 pl-12 hover:bg-gray-800 text-sm">Stock Input</a>
                <a href="{{ route('stock-opnames.index') }}" class="block px-6 py-3 pl-12 hover:bg-gray-800 text-sm">Stock Opname</a>
                <a href="{{ route('transactions.index') }}" class="block px-6 py-3 hover:bg-gray-800">Transactions</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Bar -->
            <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold">{{ $title ?? 'Dashboard' }}</h2>
                <div class="text-sm text-gray-600">
                    {{ auth()->user()->name ?? 'User' }}
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-auto p-6">
                {{ $slot }}
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
