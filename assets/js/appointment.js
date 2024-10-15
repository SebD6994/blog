// Fonction pour afficher le formulaire de modification d'un rendez-vous
function showEditForm(appointmentId) {
    // Cacher tous les formulaires de modification ouverts
    var forms = document.querySelectorAll('tr[id^="edit-row-"]');
    forms.forEach(function(row) {
        row.style.display = 'none';
    });

    // Afficher le formulaire de modification pour l'ID de rendez-vous sélectionné
    var editRow = document.getElementById('edit-row-' + appointmentId);
    editRow.style.display = 'table-row';

    // Charger les créneaux horaires pour le rendez-vous à modifier
    loadTimeSlots(appointmentId);
}

// Fonction pour masquer le formulaire de modification
function hideEditForm(appointmentId) {
    // Masquer le formulaire de modification pour l'ID de rendez-vous sélectionné
    document.getElementById('edit-row-' + appointmentId).style.display = 'none';
}

// Fonction de confirmation pour la suppression d'un rendez-vous
function confirmDelete() {
    return confirm("Êtes-vous sûr de vouloir supprimer ce rendez-vous ? Cette action est irréversible.");
}

// Fonction commune pour charger les créneaux horaires dans le formulaire (modification ou ajout)
function loadTimeSlots(appointmentId) {
    let timeSelect;
    let appointmentDate;

    // Si un ID de rendez-vous est fourni, charger le formulaire de modification
    if (appointmentId) {
        const editForm = document.getElementById('edit-form-' + appointmentId);
        appointmentDate = editForm.querySelector('input[name="appointment_date"]').value;
        timeSelect = editForm.querySelector('select[name="appointment_time"]');
    } else {
        // Sinon, charger le formulaire d'ajout
        appointmentDate = document.getElementById('appointment_date').value;
        timeSelect = document.getElementById('appointment_time');
    }

    // Effacer les options précédentes
    timeSelect.innerHTML = '';

    // Vérifier si une date a été sélectionnée
    if (!appointmentDate) {
        return; // Pas de date sélectionnée
    }

    // Requête pour récupérer les créneaux horaires disponibles pour la date sélectionnée
    fetch(`index.php?page=appointments&action=getTimeSlots&date=${appointmentDate}`)
        .then(response => response.json())
        .then(data => {
            // Vérifier si les données contiennent des créneaux horaires
            if (data && Array.isArray(data.timeSlots)) {
                data.timeSlots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot; // Le créneau horaire (ex: "10:00")
                    option.textContent = slot; // Texte affiché pour l'option
                    timeSelect.appendChild(option);
                });
            } else {
                // Gérer le cas où aucun créneau n'est disponible
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

// Fonction pour mettre à jour les créneaux horaires lors de la sélection de la date d'ajout
function updateTimeSlots() {
    loadTimeSlots(); // Appeler la fonction commune pour charger les créneaux horaires
}
