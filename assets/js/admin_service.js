// Fonction pour afficher le formulaire de modification
function showEditForm(serviceType, serviceId) {
    const editForm = document.getElementById(`edit-form-${serviceType}-${serviceId}`);
    const row = document.getElementById(`row-${serviceId}`);

    // Masquer la ligne du service
    row.style.display = 'none';
    
    // Afficher le formulaire de modification
    editForm.style.display = 'table-row'; // Affichage en tant que ligne de tableau
}

// Fonction pour masquer le formulaire de modification
function hideEditForm(serviceType, serviceId) {
    const editForm = document.getElementById(`edit-form-${serviceType}-${serviceId}`);
    const row = document.getElementById(`row-${serviceId}`);

    // Masquer le formulaire de modification
    editForm.style.display = 'none';
    
    // Afficher à nouveau la ligne du service
    row.style.display = 'table-row'; // Affichage en tant que ligne de tableau
}

// Fonction pour basculer l'affichage du formulaire de création de service
function toggleCreateServiceForm() {
    const createServiceForm = document.getElementById('create-service-form'); // Récupère le formulaire de création
    if (createServiceForm.style.display === 'none' || createServiceForm.style.display === '') {
        createServiceForm.style.display = 'block'; // Affiche le formulaire
    } else {
        createServiceForm.style.display = 'none'; // Masque le formulaire
    }
}

// Fonction pour confirmer la suppression d'un service
function confirmDelete(message) {
    return confirm(message);
}
