<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

{{-- NAVBAR mobile only --}}
<x-nav sticky class="lg:hidden">
    <x-slot:brand>
        <x-app-brand />
    </x-slot:brand>
    <x-slot:actions>
        <label for="main-drawer" class="lg:hidden mr-3">
            <x-icon name="o-bars-3" class="cursor-pointer" />
        </label>
    </x-slot:actions>
</x-nav>

{{-- MAIN --}}
<x-main full-width>
    {{-- SIDEBAR --}}
    <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

        {{-- BRAND --}}
        <x-app-brand class="p-5 pt-3" />

        {{-- MENU --}}
        <x-menu activate-by-route>

            {{-- User --}}
            @if ($user = auth()->user())
                <x-list-item :item="$user" sub-value="username" no-separator no-hover
                             class="!-mx-2 mt-2 mb-5 border-y border-y-sky-900">
                    <x-slot:actions>
                        <x-button icon="o-power" link="{{ route('logout') }}" class="btn-circle btn-ghost btn-xs"
                                  tooltip-left="logout" />
                    </x-slot:actions>
                </x-list-item>
                <x-menu-item title="Lista de Pedidos" icon="fas.list" link="{{ route('home') }}" />
                <x-menu-item title="Disponiveis para Impressão" icon="fas.print" link="{{ route('impressao') }}" />
            @endif
        </x-menu>
    </x-slot:sidebar>
    {{-- The `$slot` goes here --}}
    <x-slot:content>
        <x-theme-toggle class="btn btn-circle btn-ghost" />
        {{ $slot }}
    </x-slot:content>
</x-main>

{{--  TOAST area --}}
<x-toast />
@stack('scripts')
</body>

</html>
