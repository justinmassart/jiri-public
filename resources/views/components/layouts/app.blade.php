<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>

<body class="app">
    @include('components.header', ['page_title' => $page_title])
    @livewire('navigation')
    <main class="app__main main">
        {{ $slot }}
    </main>
    @livewire('notifications.popup')
    <script>
        document.addEventListener('livewire:navigated', function() {
            Livewire.on('updateUrl', (data) => {
                history.pushState({}, null, data[0].url);
            });

            Livewire.on('showScores', () => {
                Alpine.nextTick(() => {
                    let selectedEvaluator = null;
                    let selectedStudent = null;

                    document.querySelectorAll('th.evaluator').forEach(cell => {
                        cell.addEventListener('click', function() {
                            selectedEvaluator = this.textContent.trim();
                            highlightScoreCell();
                        });
                    });

                    document.querySelectorAll('td.student').forEach(cell => {
                        cell.addEventListener('click', function() {
                            selectedStudent = this.textContent.trim();
                            highlightScoreCell();
                        });
                    });

                    function highlightScoreCell() {
                        if (selectedEvaluator && selectedStudent) {
                            let evaluator = selectedEvaluator.replace(/\s+/g, ' ').trim();
                            let student = selectedStudent.replace(/\s+/g, ' ').trim();

                            let selector =
                                `td.score[data-evaluator="${evaluator}"][data-student="${student}"]`;

                            let scoreCells = document.querySelectorAll(selector);

                            if (scoreCells.length > 0) {
                                if (scoreCells[0].classList.contains('highlighted')) {
                                    scoreCells.forEach(cell => {
                                        cell.classList.remove('highlighted');
                                    });
                                    selectedEvaluator = null;
                                    selectedStudent = null;
                                } else {
                                    document.querySelectorAll('td.score.highlighted').forEach(
                                        cell => {
                                            cell.classList.remove('highlighted');
                                        });

                                    scoreCells.forEach(cell => {
                                        cell.classList.add('highlighted');
                                    });
                                }
                                setTimeout(() => {
                                    document.querySelectorAll('td.score.highlighted')
                                        .forEach(cell => {
                                            cell.classList.remove('highlighted');
                                        });
                                }, 2000);

                                selectedEvaluator = null;
                                selectedStudent = null;
                            }
                        }
                    }
                });
            });

            Livewire.on('showEvaluations', () => {
                Alpine.nextTick(() => {
                    const selects = document.querySelectorAll('.form-control');

                    selects.forEach(function(select) {
                        select.addEventListener('focus', function() {
                            this.children[0].textContent = '❌ - Doit présenter';
                            this.children[1].textContent = '⏳ - En cours';
                            this.children[2].textContent = '✅ - A présenté';
                        });

                        select.addEventListener('blur', function() {
                            this.children[0].textContent = '❌';
                            this.children[1].textContent = '⏳';
                            this.children[2].textContent = '✅';
                        });
                    });
                });
            });
        });
    </script>

    @livewireScripts
</body>

</html>
