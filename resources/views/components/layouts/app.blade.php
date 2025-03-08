<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-[#F5F8FA]">
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    <x-main full-width>
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-white">
            <x-app-brand class="p-5 pt-3" />
            <x-menu activate-by-route active-bg-color="bg-primary/90">
                <x-menu-separator />
                <x-list-item :item="auth()->user()" value="name" sub-value="email" no-separator no-hover
                    class="-mx-2 !-my-2 rounded">
                    <x-slot:actions>
                        <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff"
                            no-wire-navigate link="{{ route('logout') }}" />
                    </x-slot:actions>
                </x-list-item>
                <x-menu-separator />
                <x-menu-item title="Work Order" icon="c-rocket-launch" link="{{ route('work-order.index') }}"
                    class="py-3" />
                @if (auth()->user()->is_production_manager)
                    <x-menu-sub title="Reports" icon="o-clipboard-document-list">
                        <x-menu-item title="Work Order Recap" class="py-3"
                            link="{{ route('reports.work-order-recap') }}" />
                        <x-menu-item title="Operator Recap" class="py-3"
                            link="{{ route('reports.operator-recap') }}" />
                    </x-menu-sub>
                @endif
            </x-menu>
        </x-slot:sidebar>
        <x-slot:content>
            <h1 class="font-semibold">{{ $title }}</h1>
            <div class="breadcrumbs text-sm mb-5">
                <ul>
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if ($loop->last)
                            <li>{{ $breadcrumb['label'] }}</li>
                        @else
                            <li><a href="{{ $breadcrumb['route'] }}">{{ $breadcrumb['label'] }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    <x-toast />
</body>

</html>
