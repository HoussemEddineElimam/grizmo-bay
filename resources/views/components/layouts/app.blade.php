<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title ?? 'GrizmoBay'}}</title>
    @livewireStyles
    @vite('resources/js/app.js')
</head>
<body class="bg-slate-200 dark:bg-slate-700">
    @livewire('partials.navbar')
    <main class="min-h-[85vh]">
        {{ $slot }}
    </main>
    @livewire('partials.footer')

    @livewireScripts
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
    <x-livewire-alert::scripts />
</body>
</html>