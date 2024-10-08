$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultDate: moment().startOf('week'),
        editable: true,
        eventLimit: true, // permet d'afficher le lien "plus" lorsqu'il y a trop d'événements
        events: function(start, end, timezone, callback) {
            $.ajax({
                url: 'index.php?page=appointments&action=getAvailableSlots',
                dataType: 'json',
                data: {
                    start: start.unix(),
                    end: end.unix()
                },
                success: function(data) {
                    var events = [];
                    $.each(data, function(i, appointment) {
                        events.push({
                            id: appointment.id,
                            title: appointment.service_name,
                            start: appointment.appointment_date,
                            allDay: true // rend l'événement sur toute la journée
                        });
                    });
                    callback(events);
                },
                error: function() {
                    alert('Une erreur est survenue lors de la récupération des rendez-vous.');
                }
            });
        }
    });
});