<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MADO POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
    <style>
        .ts-wrapper .ts-control {
            border: 1px solid oklch(var(--bc) / 0.2);
            border-radius: var(--rounded-btn, 0.5rem);
            background-color: oklch(var(--b1));
            color: oklch(var(--bc));
            min-height: 2.5rem;
            padding: 0 0.75rem;
            font-size: 0.875rem;
            box-shadow: none;
            cursor: pointer;
        }
        .ts-wrapper.focus .ts-control {
            border-color: oklch(var(--p));
            outline: none;
            box-shadow: none;
        }
        .ts-dropdown {
            border: 1px solid oklch(var(--bc) / 0.2);
            border-radius: var(--rounded-btn, 0.5rem);
            background-color: oklch(var(--b1));
            color: oklch(var(--bc));
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            z-index: 9999;
        }
        .ts-dropdown .option {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            background-color: oklch(var(--b1));
            color: oklch(var(--bc));
        }
        .ts-dropdown .option:hover,
        .ts-dropdown .option.active {
            background-color: oklch(var(--b2));
            color: oklch(var(--bc));
        }
        .ts-dropdown .option.selected {
            background-color: oklch(var(--b2));
        }
        .ts-wrapper .ts-control input {
            color: oklch(var(--bc));
            font-size: 0.875rem;
            background-color: transparent;
        }
        .ts-wrapper .ts-control input::placeholder {
            color: oklch(var(--bc) / 0.4);
        }
        .ts-dropdown .ts-dropdown-content input {
            border: 1px solid oklch(var(--bc) / 0.2);
            border-radius: 0.375rem;
            background-color: oklch(var(--b2));
            color: oklch(var(--bc));
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            width: 100%;
            outline: none;
            margin-bottom: 0.25rem;
        }
        .ts-wrapper .item {
            background-color: oklch(var(--b2));
            color: oklch(var(--bc));
            border: 1px solid oklch(var(--bc) / 0.2);
            border-radius: 0.25rem;
            padding: 0 0.5rem;
        }
        .ts-wrapper .item .remove {
            color: oklch(var(--bc) / 0.5);
            border-left: 1px solid oklch(var(--bc) / 0.2);
            padding-left: 0.25rem;
            margin-left: 0.25rem;
        }
        .ts-wrapper .item .remove:hover {
            color: oklch(var(--er));
            background: transparent;
        }
    </style>
    @livewireStyles
</head>
<body data-theme="light">
    <div class="drawer lg:drawer-open">
        <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
        
        <!-- Page content -->
        <div class="drawer-content flex flex-col">
            <!-- Navbar -->
            <div class="navbar bg-base-100 border-b border-base-300">
                <div class="flex-1">
                    <label for="my-drawer-2" class="btn btn-ghost drawer-button lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </label>
                    <div class="px-2 mx-2 font-bold text-xl">{{ $title ?? 'Dashboard' }}</div>
                </div>
                <div class="flex-none gap-2">
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-circle avatar cursor-pointer">
                            <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </label>
                        <ul tabindex="0" class="dropdown-content z-50 menu p-2 shadow bg-base-100 rounded-box w-52">
                            <li class="menu-title">
                                <span>{{ auth()->user()->name }}</span>
                            </li>
                            <li>
                                <a href="{{ route('change-password') }}">Change Password</a>
                            </li>
                            <li>
                                <a onclick="document.getElementById('logout-form').submit()">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
            </div>

            <!-- Page content -->
            <div class="flex-1 overflow-auto p-4 lg:p-6">
                {{ $slot }}
            </div>
        </div>

        <!-- Sidebar -->
        <div class="drawer-side">
            <label for="my-drawer-2" class="drawer-overlay"></label>
            <ul class="menu p-4 w-80 min-h-full bg-slate-800 text-base-content space-y-2">
                <li class="menu-title mb-4">
                    <span class="text-xl font-bold text-white">MADO POS</span>
                </li>
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                        <x-icon.home />
                        Dashboard
                    </a>
                </li>
                @if(auth()->user()->role === 'SUPERADMIN')
                    <li>
                        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                            <x-icon.users />
                            Users
                        </a>
                    </li>
                @endif
                @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'SUPERADMIN')
                    <li>
                        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                            <x-icon.users />
                            Customers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('price-list-types.index') }}" class="{{ request()->routeIs('price-list-types.*') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                            <x-icon.archive />
                            Price List Types
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                            <x-icon.package />
                            Items
                        </a>
                    </li>
                    <li>
                        <details @if(request()->routeIs('stock-input.*', 'stock-opname.*')) open @endif>
                            <summary class="text-white hover:bg-slate-700">
                                <x-icon.inbox />
                                Stock Management
                            </summary>
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('stock-input.index') }}" class="{{ request()->routeIs('stock-input.*') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                                        <x-icon.plus-circle />
                                        Stock Input
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('stock-opname.index') }}" class="{{ request()->routeIs('stock-opname.*') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                                        <x-icon.clipboard />
                                        Stock Opname
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                @endif
                <li>
                    <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                        <x-icon.shopping-cart />
                        Transactions
                    </a>
                </li>
                @if(auth()->user()->role === 'SUPERADMIN')
                    <li>
                        <details @if(request()->routeIs('reports.*')) open @endif>
                            <summary class="text-white hover:bg-slate-700">
                                <x-icon.chart-bar />
                                Sales Reports
                            </summary>
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('reports.by-products') }}" class="{{ request()->routeIs('reports.by-products') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                                        <x-icon.package />
                                        By Products
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.by-transactions') }}" class="{{ request()->routeIs('reports.by-transactions') ? 'active bg-primary text-primary-content' : 'text-white hover:bg-slate-700' }}">
                                        <x-icon.shopping-cart />
                                        By Transactions
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    @stack('scripts')
    <x-notification />
</body>
</html>
