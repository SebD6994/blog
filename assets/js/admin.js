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

    window.showEditForm = function(appointmentId) {
        // Cacher tous les formulaires de modification ouverts
        var forms = document.querySelectorAll('tr[id^="edit-row-"]');
        forms.forEach(function(row) {
            row.style.display = 'none';
        });

        // Afficher le formulaire de modification pour l'ID de rendez-vous sélectionné
        var editRow = document.getElementById('edit-row-' + appointmentId);
        if (editRow) {
            editRow.style.display = 'table-row';
            console.log(`Showing edit form for appointment ID: ${appointmentId}`);
        } else {
            console.error(`Edit form not found for appointment ID: ${appointmentId}`);
        }
    }

    window.hideEditForm = function(appointmentId) {
        // Masquer le formulaire de modification pour l'ID de rendez-vous sélectionné
        var editRow = document.getElementById('edit-row-' + appointmentId);
        if (editRow) {
            editRow.style.display = 'none';
            console.log(`Hiding edit form for appointment ID: ${appointmentId}`);
        } else {
            console.error(`Edit form not found for appointment ID: ${appointmentId}`);
        }
    }

    window.confirmDelete = function() {
        return confirm("Êtes-vous sûr de vouloir supprimer ce rendez-vous ? Cette action est irréversible.");
    }
});
