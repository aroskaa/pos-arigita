<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-ag.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            window.printReceipt = function(url, paperSize) {
                let size = paperSize || localStorage.getItem('pos_receipt_paper_size') || '58';
                let targetUrl;
                try {
                    targetUrl = new URL(url, window.location.origin);
                    if (!targetUrl.searchParams.has('paper') && !targetUrl.searchParams.has('size')) {
                        targetUrl.searchParams.set('paper', size);
                    }
                } catch (e) {
                    targetUrl = url + (url.includes('?') ? '&' : '?') + 'paper=' + size;
                }

                let iframe = document.getElementById('receipt-print-iframe');
                if (!iframe) {
                    iframe = document.createElement('iframe');
                    iframe.id = 'receipt-print-iframe';
                    iframe.style.position = 'fixed';
                    iframe.style.right = '0';
                    iframe.style.bottom = '0';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.style.border = '0';
                    iframe.style.opacity = '0';
                    document.body.appendChild(iframe);
                }

                const printFrame = () => {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (e) {
                        console.error('Receipt print error:', e);
                    }
                };

                iframe.onload = function() {
                    setTimeout(printFrame, 250);
                };

                iframe.src = targetUrl.toString();
            };
        </script>

        @livewireScripts
    </body>
</html>
