document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour gérer le toggle de la section
    function toggleSection(sectionTitle) {
        const content = sectionTitle.nextElementSibling; // L'élément de contenu suivant
        if (content.style.display === 'block') {
            content.style.display = 'none'; // Masquer la section
        } else {
            content.style.display = 'block'; // Afficher la section
        }
    }

    // Ajoutez des écouteurs d'événements pour chaque titre de section
    const sectionTitles = document.querySelectorAll('.section-title');

    sectionTitles.forEach(title => {
        title.addEventListener('click', function() {
            console.log(`Toggling section for: ${title.textContent}`);
            toggleSection(title);
        });
    });

    // Fonction pour afficher le formulaire de création
    function toggleCreateForm() {
        const createForm = document.getElementById('create-service-form'); // Formulaire de création
        const toggleButton = document.getElementById('toggle-create-form'); // Bouton pour afficher/masquer le formulaire
        
        if (createForm.style.display === 'none' || createForm.style.display === '') {
            createForm.style.display = 'block'; // Afficher le formulaire
            toggleButton.textContent = 'Annuler'; // Changer le texte du bouton
        } else {
            createForm.style.display = 'none'; // Masquer le formulaire
            toggleButton.textContent = 'Ajouter un Service'; // Rétablir le texte du bouton
        }
    }

    // Écoutez le clic sur le bouton d'ajout de service
    const toggleButton = document.getElementById('toggle-create-form');
    if (toggleButton) {
        toggleButton.addEventListener('click', toggleCreateForm);
    }

    window.showEditForm = function(serviceId) {
        // Cacher tous les formulaires de modification ouverts
        var forms = document.querySelectorAll('tr[id^="edit-form-"]');
        forms.forEach(function(row) {
            row.style.display = 'none';
        });

        // Afficher le formulaire de modification pour l'ID de service sélectionné
        var editRow = document.getElementById('edit-form-' + serviceId);
        if (editRow) {
            editRow.style.display = 'table-row';
            console.log(`Showing edit form for service ID: ${serviceId}`);
        } else {
            console.error(`Edit form not found for service ID: ${serviceId}`);
        }
    }

    window.hideEditForm = function(serviceId) {
        // Masquer le formulaire de modification pour l'ID de service sélectionné
        var editRow = document.getElementById('edit-form-' + serviceId);
        if (editRow) {
            editRow.style.display = 'none';
            console.log(`Hiding edit form for service ID: ${serviceId}`);
        } else {
            console.error(`Edit form not found for service ID: ${serviceId}`);
        }
    }

    window.confirmDelete = function() {
        return confirm("Êtes-vous sûr de vouloir supprimer ce service ? Cette action est irréversible.");
    }
});