@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-page">
        <x-layouts::partials.header-bar
            :notifications-route="Route::has('student.notifications.index') ? 'student.notifications.index' : null"
        >
            <x-slot:leading>
                <flux:sidebar.toggle class="text-white" icon="bars-2" inset="left" />
            </x-slot:leading>
        </x-layouts::partials.header-bar>

        <flux:sidebar collapsible sticky class="border-e border-zinc-200 bg-white">
            <flux:sidebar.nav>
                <flux:sidebar.item icon="layout-grid" :href="route('student.dashboard')" :current="request()->routeIs('student.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                <flux:sidebar.item
                    icon="clipboard-document-list"
                    :href="Route::has('student.applications.index') ? route('student.applications.index') : '#'"
                    :current="request()->routeIs('student.applications.*')"
                    wire:navigate
                >
                    {{ __('My Applications') }}
                </flux:sidebar.item>

                <flux:sidebar.item
                    icon="plus-circle"
                    :href="Route::has('student.applications.create') ? route('student.applications.create') : '#'"
                    wire:navigate
                >
                    {{ __('Submit Application') }}
                </flux:sidebar.item>

                <flux:sidebar.item
                    icon="megaphone"
                    :href="Route::has('student.announcements.index') ? route('student.announcements.index') : '#'"
                    :current="request()->routeIs('student.announcements.*')"
                    wire:navigate
                >
                    {{ __('Announcements') }}
                </flux:sidebar.item>

                <flux:sidebar.item
                    icon="bell"
                    :href="Route::has('student.notifications.index') ? route('student.notifications.index') : '#'"
                    :current="request()->routeIs('student.notifications.*')"
                    wire:navigate
                >
                    {{ __('Notifications') }}
                </flux:sidebar.item>
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
