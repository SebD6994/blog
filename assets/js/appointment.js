function showEditForm(appointmentId) {
    var forms = document.querySelectorAll('tr[id^="edit-row-"]');
    forms.forEach(function(row) {
        row.style.display = 'none';
    });

    var editRow = document.getElementById('edit-row-' + appointmentId);
    editRow.style.display = 'table-row';
    loadTimeSlots(appointmentId);
}

function hideEditForm(appointmentId) {
    document.getElementById('edit-row-' + appointmentId).style.display = 'none';
}

function confirmDelete() {
    return confirm("Êtes-vous sûr de vouloir supprimer ce rendez-vous ? Cette action est irréversible.");
}

function loadTimeSlots(appointmentId) {
    let timeSelect;
    let appointmentDate;

    if (appointmentId) {
        const editForm = document.getElementById('edit-form-' + appointmentId);
        appointmentDate = editForm.querySelector('input[name="appointment_date"]').value;
        timeSelect = editForm.querySelector('select[name="appointment_time"]');
    } else {
        appointmentDate = document.getElementById('appointment_date').value;
        timeSelect = document.getElementById('appointment_time');
    }

    timeSelect.innerHTML = '';

    if (!appointmentDate) {
        return;
    }

    fetch(`index.php?page=appointments&action=getTimeSlots&date=${appointmentDate}`)
        .then(response => response.json())
        .then(data => {
            if (data && Array.isArray(data.timeSlots)) {
                data.timeSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Aucun créneau disponible';
                timeSelect.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des créneaux horaires:', error);
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Erreur de chargement des créneaux';
            timeSelect.appendChild(option);
        });
}

function updateTimeSlots() {
    loadTimeSlots();
}