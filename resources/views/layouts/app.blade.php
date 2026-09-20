<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IT Support Panel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons (kept temporarily — icons are pervasive, low risk to leave as-is) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Bootstrap CSS/JS: TEMPORARY compatibility shim. Most admin views (~60) still use
         Bootstrap markup (.btn/.card/.modal/data-bs-*) and were not converted to Tailwind
         in this pass — only layouts/app.blade.php + the new cabinets/medical-staff views
         were. Remove once every view extending this layout is migrated to Tailwind. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- TEMPORARY: custom classes the old Bootstrap-era inline <style> block defined,
         still referenced by every unconverted view (.stats-card is used on ~60 pages).
         Remove once those views are migrated to Tailwind. --}}
    <style>
        .stats-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .collapse.show {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .form-label {
                font-size: 0.875rem;
                margin-bottom: 0.25rem;
            }

            .form-control, .form-select {
                font-size: 0.875rem;
                padding: 0.5rem;
            }

            .btn {
                padding: 0.5rem 0.75rem;
            }

            .table-responsive {
                border: none;
            }

            .table {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 0.4rem 0.25rem;
            }

            .table thead th {
                font-size: 0.75rem;
            }

            .btn-group-sm > .btn,
            .btn-sm {
                padding: 0.25rem 0.4rem;
                font-size: 0.65rem;
            }

            .badge {
                font-size: 0.65rem;
                padding: 0.35rem 0.4rem;
            }

            code {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar backdrop (mobile) -->
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-black/50 lg:hidden"
            style="display: none;"
        ></div>

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 w-64 transform overflow-y-auto bg-gradient-to-b from-blue-900 to-blue-800 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
        >
            <div class="p-4">
                <h4 class="mb-4 flex items-center text-lg font-semibold text-white">
                    <i class="bi bi-hospital me-2"></i>
                    <span class="ms-2">IT Support Panel</span>
                </h4>

                <nav class="flex flex-col gap-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-speedometer2"></i>
                        Головна
                    </a>

                    @if(Auth::user()->role === 'admin')

                    <a href="{{ route('repairs.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('repairs.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-tools"></i>
                        Заявки на ремонт
                    </a>

                    <a href="{{ route('cartridges.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('cartridges.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-printer"></i>
                        Заміна картриджів
                    </a>

                    <a href="{{ route('repair-orders.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('repair-orders.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-tools"></i>
                        Облік ремонтів
                    </a>

                    <a href="{{ route('purchase-requests.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('purchase-requests.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-cart-plus"></i>
                        Заявки на закупівлю
                    </a>

                    <a href="{{ route('writeoff-requests.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('writeoff-requests.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-file-earmark-minus"></i>
                        Заявки на списання
                    </a>

                    <a href="{{ route('branches.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('branches.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-building"></i>
                        Філії
                    </a>

                    <a href="{{ route('medical-staff.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('medical-staff.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-person-badge"></i>
                        Медперсонал
                    </a>

                    <a href="{{ route('cabinets.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('cabinets.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-door-open"></i>
                        Кабінети
                    </a>

                    <a href="{{ route('inventory.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('inventory.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-pc-display"></i>
                        Інвентар
                    </a>

                    <a href="{{ route('warehouse.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('warehouse.index', 'warehouse.show', 'warehouse.create', 'warehouse.edit', 'warehouse.show-by-name') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-box-seam"></i>
                        Склад
                    </a>

                    <a href="{{ route('warehouse.movements') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('warehouse.movements') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-arrow-left-right"></i>
                        Рух товарів
                    </a>

                    <a href="{{ route('inventory.export.form') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('inventory.export.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-file-earmark-excel"></i>
                        Експорт в Excel
                    </a>

                    <a href="{{ route('work-logs.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('work-logs.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-journal-text"></i>
                        Журнал робіт
                    </a>
                    @endif

                    @if(Auth::user()->role === 'warehouse_keeper')

                    <a href="{{ route('warehouse.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('warehouse.index', 'warehouse.show', 'warehouse.create', 'warehouse.edit', 'warehouse.show-by-name') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-box-seam"></i>
                        Склад
                    </a>

                    <a href="{{ route('inventory.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('inventory.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-pc-display"></i>
                        Інвентар обладнання
                    </a>

                    <a href="{{ route('repair-orders.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('repair-orders.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-tools"></i>
                        Облік ремонтів
                    </a>

                    <a href="{{ route('purchase-requests.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('purchase-requests.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-cart-plus"></i>
                        Заявки на закупівлю
                    </a>

                    <a href="{{ route('writeoff-requests.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('writeoff-requests.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-file-earmark-minus"></i>
                        Заявки на списання
                    </a>

                    <a href="{{ route('warehouse.movements') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('warehouse.movements') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-arrow-left-right"></i>
                        Рух товарів
                    </a>
                    @endif

                    @if(Auth::user()->role === 'director')
                    <hr class="my-2 border-white/20">

                    <a href="{{ route('branch-analytics.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('branch-analytics.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-graph-up"></i>
                        Аналітика філій
                    </a>

                    <a href="{{ route('director-inventory.warehouse') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('director-inventory.warehouse') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-box-seam"></i>
                        Інвентар складу
                    </a>

                    <a href="{{ route('director-inventory.equipment') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('director-inventory.equipment') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-pc-display"></i>
                        Інвентар кабінетів
                    </a>

                    <a href="{{ route('director-inventory.forecasting') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('director-inventory.forecasting') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-crystal-ball"></i>
                        Прогнозування витрат
                    </a>

                    <a href="{{ route('work-logs.index') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('work-logs.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi bi-journal-text"></i>
                        Журнал робіт
                    </a>
                    @endif
                </nav>

                <hr class="my-3 border-white/20">

                <nav class="flex flex-col gap-1">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-blue-100 transition hover:bg-white/10 hover:text-white">
                        <i class="bi bi-person"></i>
                        Профіль
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-start text-sm font-medium text-blue-100 transition hover:bg-white/10 hover:text-white">
                            <i class="bi bi-box-arrow-right"></i>
                            Вихід
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex min-w-0 flex-1 flex-col">
            <!-- Mobile sticky header -->
            <div class="sticky top-0 z-20 flex items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 lg:hidden">
                <button type="button" @click="sidebarOpen = true" class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100">
                    <i class="bi bi-list text-xl"></i>
                </button>
                <span class="font-semibold text-gray-800">IT Support Panel</span>
            </div>

            <main class="flex-1 p-4 lg:p-8">
                <!-- Page Header -->
                <div class="mb-4 flex flex-col justify-between gap-2 sm:flex-row sm:items-center lg:mb-6">
                    <h1 class="text-xl font-semibold text-gray-900 lg:text-2xl">@yield('title', 'Головна')</h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                            @switch(Auth::user()->role)
                                @case('admin') Адмін @break
                                @case('director') Директор @break
                                @case('warehouse_keeper') Склад @break
                                @default Користувач
                            @endswitch
                        </span>
                        <span class="hidden text-sm text-gray-500 sm:inline">{{ Auth::user()->name }}</span>
                        <span class="text-sm text-gray-500 sm:hidden">{{ Str::limit(Auth::user()->name, 20) }}</span>
                    </div>
                </div>

                <!-- Alerts -->
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                         class="mb-4 flex items-start justify-between gap-2 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        <span>{{ session('success') }}</span>
                        <button type="button" @click="show = false" class="text-green-600 hover:text-green-800">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                         class="mb-4 flex items-start justify-between gap-2 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <span>{{ session('error') }}</span>
                        <button type="button" @click="show = false" class="text-red-600 hover:text-red-800">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition
                         class="mb-4 flex items-start justify-between gap-2 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" @click="show = false" class="text-red-600 hover:text-red-800">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Bootstrap JS: TEMPORARY compatibility shim, see head comment. Loaded after Alpine
         so Alpine components (mobile sidebar) still work; Bootstrap only backs the
         unconverted views' own modals/dropdowns/popovers. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
