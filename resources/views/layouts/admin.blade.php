@props(['title' => null])

@php
    $isSuperAdmin = auth()->user()?->hasRole(\App\Enums\RoleName::SuperAdmin->value);
    $dashboardRoute = $isSuperAdmin ? 'super-admin.dashboard' : 'admin.dashboard';
    $routePrefix = $isSuperAdmin ? 'super-admin.' : 'admin.';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-page">
        <x-layouts::partials.header-bar
            :notifications-route="Route::has($routePrefix.'notifications.index') ? $routePrefix.'notifications.index' : null"
        >
            <x-slot:leading>
                <flux:sidebar.toggle class="text-white lg:hidden" icon="bars-2" inset="left" />
            </x-slot:leading>
        </x-layouts::partials.header-bar>

        <flux:sidebar collapsible="mobile" sticky class="border-e border-zinc-200 bg-white">
            <flux:sidebar.nav>
                <flux:sidebar.item icon="layout-grid" :href="route($dashboardRoute)" :current="request()->routeIs($dashboardRoute)" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                <flux:sidebar.item
                    icon="clipboard-document-list"
                    :href="Route::has($routePrefix.'applications.index') ? route($routePrefix.'applications.index') : '#'"
                    :current="request()->routeIs($routePrefix.'applications.*')"
                    wire:navigate
                >
                    {{ __('Applications') }}
                </flux:sidebar.item>

                @if ($isSuperAdmin)
                    <flux:sidebar.item
                        icon="user-group"
                        :href="Route::has('super-admin.admins.index') ? route('super-admin.admins.index') : '#'"
                        :current="request()->routeIs('super-admin.admins.*')"
                        wire:navigate
                    >
                        {{ __('Admin Officers') }}
                    </flux:sidebar.item>
                @endif

                <flux:sidebar.item
                    icon="users"
                    :href="Route::has($routePrefix.'students.index') ? route($routePrefix.'students.index') : '#'"
                    :current="request()->routeIs($routePrefix.'students.*')"
                    wire:navigate
                >
                    {{ __('Students') }}
                </flux:sidebar.item>

                @if ($isSuperAdmin)
                    <flux:sidebar.item
                        icon="tag"
                        :href="Route::has('super-admin.categories.index') ? route('super-admin.categories.index') : '#'"
                        :current="request()->routeIs('super-admin.categories.*')"
                        wire:navigate
                    >
                        {{ __('Categories') }}
                    </flux:sidebar.item>
                @endif

                <flux:sidebar.item
                    icon="megaphone"
                    :href="Route::has($routePrefix.'announcements.index') ? route($routePrefix.'announcements.index') : '#'"
                    :current="request()->routeIs($routePrefix.'announcements.*')"
                    wire:navigate
                >
                    {{ __('Announcements') }}
                </flux:sidebar.item>

                <flux:sidebar.item
                    icon="bell"
                    :href="Route::has($routePrefix.'notifications.index') ? route($routePrefix.'notifications.index') : '#'"
                    :current="request()->routeIs($routePrefix.'notifications.*')"
                    wire:navigate
                >
                    {{ __('Notifications') }}
                </flux:sidebar.item>

                @if ($isSuperAdmin)
                    <flux:sidebar.item
                        icon="shield-check"
                        :href="Route::has('super-admin.audit-log.index') ? route('super-admin.audit-log.index') : '#'"
                        :current="request()->routeIs('super-admin.audit-log.*')"
                        wire:navigate
                    >
                        {{ __('Audit Log') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="cog"
                        :href="Route::has('super-admin.settings.edit') ? route('super-admin.settings.edit') : '#'"
                        :current="request()->routeIs('super-admin.settings.*')"
                        wire:navigate
                    >
                        {{ __('System Settings') }}
                    </flux:sidebar.item>
                @endif
            </flux:sidebar.nav>
        </flux:sidebar>

        <flux:main container>
            {{ $slot }}
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
