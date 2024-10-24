// Fonction pour afficher le formulaire de modification du rendez-vous
function showEditForm(type, id) {
    const editForm = document.getElementById(`edit-form-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    // Masquer la ligne du rendez-vous
    row.style.display = 'none';
    
    // Afficher le formulaire de modification
    editForm.style.display = 'table-row'; // Affichage en tant que ligne de tableau

    // Ajouter un listener pour mettre à jour les créneaux lorsque la date change
    const dateInput = document.getElementById(`edit-date-${id}`);
    dateInput.addEventListener('change', function() {
        updateAvailableTimeSlots(id, this.value);
    });
}

// Fonction pour masquer le formulaire de modification du rendez-vous
function hideEditForm(type, id) {
    const editForm = document.getElementById(`edit-form-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    // Masquer le formulaire de modification
    editForm.style.display = 'none';
    
    // Afficher à nouveau la ligne du rendez-vous
    row.style.display = 'table-row'; // Affichage en tant que ligne de tableau
}

// Fonction pour la confirmation de suppression
window.confirmDelete = function(message) {
    return confirm(message); // Affiche une boîte de confirmation
};