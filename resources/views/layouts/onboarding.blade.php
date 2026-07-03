<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('faveicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('faveicon.svg') }}">
    <title>Onboarding — {{ config('app.name', 'AI CS') }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <div class="flex items-center gap-2 mb-8 justify-center">
            <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white font-bold text-sm">AI</div>
            <span class="font-semibold text-slate-900">{{ config('app.name', 'AI Customer Service') }}</span>
        </div>

        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
