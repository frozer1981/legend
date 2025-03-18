<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Odds</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.1/echo.iife.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f4f4f4; }
        h1 { color: #333; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        th { background: #333; color: white; }
    </style>
</head>
<body>

<h1>Live Sports Odds</h1>
<table>
    <thead>
    <tr>
        <th>Sport</th>
        <th>League</th>
        <th>Teams</th>
        <th>Home Win</th>
        <th>Draw</th>
        <th>Away Win</th>
    </tr>
    </thead>
    <tbody id="odds-table">
    <!-- Зареждаме всички съществуващи събития от PHP -->
    @foreach($events as $event)
        @if($event->odds)
            <tr>
                <td>{{ ucfirst($event->sport) }}</td>
                <td>{{ $event->league }}</td>
                <td>
                    {{ implode(' vs ', $event->teams->pluck('name')->toArray()) }}
                </td>
                <td>{{ $event->odds->home_win }}</td>
                <td>{{ $event->odds->draw ?? '-' }}</td>
                <td>{{ $event->odds->away_win }}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>

<script>
    // Enable Pusher debugging (disable in production)
    Pusher.logToConsole = true;

    // Initialize Echo with Pusher
    let echo = new Echo({
        broadcaster: "pusher",
        key: "{{ env('PUSHER_APP_KEY') }}",
        cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
        forceTLS: true
    });

    // Функция за добавяне на нови коефициенти
    function addOddToTable(event) {
        let table = document.getElementById("odds-table");

        let teams = event.teams.map(team => team.name).join(" vs "); // Получаваме имената на отборите

        let row = `
            <tr>
                <td>${event.sport.charAt(0).toUpperCase() + event.sport.slice(1)}</td>
                <td>${event.league}</td>
                <td>${teams}</td>
                <td>${event.odds.home_win}</td>
                <td>${event.odds.draw ?? '-'}</td>
                <td>${event.odds.away_win}</td>
            </tr>
        `;

        table.innerHTML = row + table.innerHTML; // Добавя нов ред най-отгоре
    }

    // Зареждаме всички събития от API при зареждане
    fetch('/api/events')
        .then(response => response.json())
        .then(events => {
            let table = document.getElementById("odds-table");
            table.innerHTML = ""; // Изчистваме "Loading events..."

            events.forEach(event => {
                // ✅ Проверяваме дали event има odds и дали коефициентите не са null
                if (event.odds && event.odds.home_win !== null && event.odds.away_win !== null) {
                    addOddToTable(event);
                } else {
                    console.warn("Event has no odds or odds are invalid:", event);
                }
            });
        })
        .catch(error => console.error("Error loading events:", error));

    echo.channel("odds-updates")
        .listen(".OddsUpdated", (data) => {
            console.log("New Odds Received:", data);

            if (!data.odd || !data.odd.event_id) {
                console.error("Invalid odds data:", data);
                return;
            }

            // Взимаме актуализираното събитие от API, за да добавим пълната информация
            fetch(`/api/events/${data.odd.event_id}`)
                .then(response => response.json())
                .then(event => addOddToTable(event))
                .catch(error => console.error("Error fetching updated event:", error));
        });

    function addOddToTable(event) {
        let table = document.getElementById("odds-table");

        let teams = event.teams.map(team => team.name).join(" vs "); // Извличаме имената на отборите

        let row = `
        <tr>
            <td>${event.sport.charAt(0).toUpperCase() + event.sport.slice(1)}</td>
            <td>${event.league}</td>
            <td>${teams}</td>
            <td>${event.odds.home_win}</td>
            <td>${event.odds.draw ?? '-'}</td>
            <td>${event.odds.away_win}</td>
        </tr>
    `;

        table.innerHTML = row + table.innerHTML; // Добавяме нов ред най-отгоре
    }
</script>

</body>
</html>
