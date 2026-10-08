<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-page antialiased">
        <div class="flex min-h-screen flex-wrap">
            <section class="flex min-h-[560px] flex-1 basis-[520px] flex-col justify-between gap-12 bg-plum-dark p-12 text-white sm:p-14">
                <span class="text-[13px] font-semibold tracking-[0.08em] text-white/72 uppercase">Department of Software Engineering</span>

                <div class="flex max-w-[520px] flex-col gap-7">
                    <x-superior-logo white class="h-14 w-auto" />

                    <h1 class="font-display m-0 text-[48px] leading-[1.1] font-medium tracking-tight">Student Support Portal</h1>

                    <p class="m-0 text-[17px] leading-relaxed text-white/82">
                        Submit your application online, follow every step of its progress, and hear back from the Admin Office without a single trip to the counter.
                    </p>

                    <div class="flex flex-col gap-4 pt-2">
                        <div class="flex items-start gap-3.5">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white/10">
                                <flux:icon icon="document-text" variant="outline" class="size-4.5 text-white" />
                            </span>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[15px] font-semibold">One application, one tracking number</span>
                                <span class="text-sm text-white/70">Every request gets an ID like SC-2026-000124.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3.5">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white/10">
                                <flux:icon icon="clock" variant="outline" class="size-4.5 text-white" />
                            </span>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[15px] font-semibold">A clear timeline</span>
                                <span class="text-sm text-white/70">See when it was reviewed, updated and resolved.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3.5">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white/10">
                                <flux:icon icon="envelope" variant="outline" class="size-4.5 text-white" />
                            </span>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[15px] font-semibold">Updates on your university email</span>
                                <span class="text-sm text-white/70">You are notified the moment anything changes.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <span class="text-[13px] text-white/60">&copy; {{ now()->year }} Superior University, Lahore</span>
            </section>

            <section class="flex flex-1 basis-[480px] items-center justify-center p-6 sm:p-12">
                <div class="w-full max-w-[420px]">
                    {{ $slot }}
                </div>
            </section>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
