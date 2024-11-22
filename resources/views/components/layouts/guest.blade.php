<!DOCTYPE html>
<html class="relative" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>

<body class="app-guest">
    @include('components.header-guest')
    <main class="app__main main">
        {{ $slot }}
    </main>
    @livewire('notifications.popup')
    @livewireScripts
    <script>
        document.addEventListener('livewire:navigated', function() {
            Alpine.nextTick(() => {
                const input = document.querySelector('#numberInput');
                const score = document.querySelector('.score__container__score');

                score.addEventListener('click', () => {
                    input.focus();
                });
            });
            /* let seconds = 0;
            let minutes = 0;
            let displaySeconds = 0;
            let displayMinutes = 0;
            let interval = null;

            const startTimer = () => {
                interval = null;
                interval = setInterval(() => {
                    seconds++;

                    if (seconds / 60 === 1) {
                        seconds = 0;
                        minutes++;
                    }

                    displaySeconds = seconds < 10 ? "0" + seconds.toString() : seconds;
                    displayMinutes = minutes < 10 ? "0" + minutes.toString() : minutes;

                    document.getElementById("timer").textContent = displayMinutes + ":" +
                        displaySeconds;
                }, 1000);
            }

            const stopTimer = () => {
                clearInterval(interval);
            }

            Livewire.on('startChrono', () => {
                startTimer();
            });

            Livewire.on('stopChrono', () => {
                stopTimer();
            }); */
        });
    </script>
</body>

</html>
