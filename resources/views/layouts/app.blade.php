@props(['title' => ''])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}{{ !empty($title) ? ' | ' . $title : '' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-background text-foreground">
    <div class="flex h-screen overflow-hidden"
         x-data="{ open: $persist(true).as('sb') }"
         @toggle-sidebar.window="open = !open"
         @open-sidebar.window="open = true"
    >
        @include('layouts.sidebar')

        {{-- Contenido principal --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">
            @include('layouts.topbar')

            <main class="flex-1 overflow-y-auto bg-background p-6 lg:p-8">
                @isset($header)
                    <div class="py-5 px-4 sm:px-6 lg:px-8 border-b border-border">
                        {{ $header }}
                    </div>
                @endisset

                <div class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </div>
            </main>

            @include('layouts.footer')
        </div>
    </div>

    <x-toaster />
    <livewire:components.delete-modal />
    @livewireScripts
    @stack('scripts')

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-print-window', (event) => {
                let url = event.url;
                if (url) {
                    const params = new URLSearchParams(window.location.search);
                    const periodFilter = params.get('filters[date_period]');
                    if (periodFilter && !url.includes('period=')) {
                        url += (url.includes('?') ? '&' : '?') + 'period=' + periodFilter;
                    }
                    window.open(url, '_blank');
                }
            });
        });

        window.currencySymbol = "{{ \App\Models\Setting::get('currency_symbol', 'Rp') }}";
        window.currencyPosition = "{{ \App\Models\Setting::get('currency_position', 'left') }}";
        window.currencyFraction = parseInt("{{ \App\Models\Setting::get('currency_fraction_digits', 0) }}");
        window.thousandSeparator = "{{ \App\Models\Setting::get('currency_thousand_separator', '.') }}";
        window.decimalSeparator = "{{ \App\Models\Setting::get('currency_decimal_separator', ',') }}";

        window.formatMoney = function(val) {
            let amount = parseFloat(val) || 0;
            let isNegative = amount < 0;
            amount = Math.abs(amount);
            let strAmount = amount.toFixed(window.currencyFraction);
            let parts = strAmount.split('.');
            let integerPart = parts[0];
            let decimalPart = parts.length > 1 ? window.decimalSeparator + parts[1] : '';
            let rgx = /(\d+)(\d{3})/;
            while (rgx.test(integerPart)) {
                integerPart = integerPart.replace(rgx, '$1' + window.thousandSeparator + '$2');
            }
            let num = integerPart + decimalPart;
            if (isNegative) num = '-' + num;
            return window.currencyPosition === 'left' ? window.currencySymbol + ' ' + num : num + ' ' + window.currencySymbol;
        };
        window.formatCurrency = window.formatMoney;
    </script>
</body>

</html>
