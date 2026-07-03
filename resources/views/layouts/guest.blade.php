<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('faveicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('faveicon.svg') }}">
    <title>{{ config('app.name', 'AI CS') }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="mb-6 text-center">
            <img src="{{asset('logo-egos.png')}}" alt="" srcset="">
            {{-- <div class="inline-flex w-10 h-10 rounded-xl bg-teal-600 items-center justify-center text-white font-bold">
            </div> --}}
            {{-- <h1 class="mt-3 text-xl font-semibold text-slate-900">{{ config('app.name', 'AI Customer Service') }}</h1> --}}
        </div>
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
