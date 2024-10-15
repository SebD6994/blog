document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridWeek,timeGridDay'
        },
        contentHeight: '350px',
        firstDay: 1,
        locale: 'fr',
        
        // Événement pour cliquer sur une date (fonctionnalité supprimée)
        dateClick: function(info) {
            alert("Vous avez cliqué sur la date : " + info.dateStr);
        }
    });

    calendar.render();
});