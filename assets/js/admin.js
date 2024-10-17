document.addEventListener('DOMContentLoaded', function() {
    function toggleSection(sectionTitle) {
        const sectionsContainer = sectionTitle.nextElementSibling; // L'élément de contenu suivant

        // Fermer toutes les autres sections
        const allSections = document.querySelectorAll('.sections-container');
        allSections.forEach(section => {
            if (section !== sectionsContainer) {
                section.style.display = 'none'; // Masquer les autres sections
            }
        });

        // Ouvrir ou fermer la section cliquée
        if (sectionsContainer.style.display === 'block') {
            sectionsContainer.style.display = 'none'; // Masquer la section
        } else {
            sectionsContainer.style.display = 'block'; // Afficher la section

            // Faire défiler jusqu'à la section ouverte
            sectionsContainer.scrollIntoView({
                behavior: 'smooth', // Animation fluide
                block: 'start' // Aligner le début de la section en haut
            });
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

    // Fonction pour afficher le formulaire de création de service
    function toggleCreateServiceForm() {
        const createServiceForm = document.getElementById('create-service-form'); // Formulaire de création
        const toggleButton = document.getElementById('toggle-create-service-form'); // Bouton pour afficher/masquer le formulaire
        
        if (createServiceForm.style.display === 'none' || createServiceForm.style.display === '') {
            createServiceForm.style.display = 'block'; // Afficher le formulaire
            toggleButton.textContent = 'Annuler'; // Changer le texte du bouton
        } else {
            createServiceForm.style.display = 'none'; // Masquer le formulaire
            toggleButton.textContent = 'Ajouter un Service'; // Rétablir le texte du bouton
        }
    }

    // Fonction pour afficher le formulaire de création d'actualité
    function toggleCreateNewsForm() {
        const createNewsForm = document.getElementById('create-news-form'); // Formulaire d'ajout d'actualité
        const toggleButton = document.getElementById('toggle-create-news-form'); // Bouton pour afficher/masquer le formulaire
        
        if (createNewsForm.style.display === 'none' || createNewsForm.style.display === '') {
            createNewsForm.style.display = 'block'; // Afficher le formulaire
            toggleButton.textContent = 'Annuler'; // Changer le texte du bouton
        } else {
            createNewsForm.style.display = 'none'; // Masquer le formulaire
            toggleButton.textContent = 'Ajouter une Actualité'; // Rétablir le texte du bouton
        }
    }

    // Écoutez le clic sur les boutons d'ajout
    const toggleServiceButton = document.getElementById('toggle-create-service-form');
    if (toggleServiceButton) {
        toggleServiceButton.addEventListener('click', toggleCreateServiceForm);
    }

    const toggleNewsButton = document.getElementById('toggle-create-news-form');
    if (toggleNewsButton) {
        toggleNewsButton.addEventListener('click', toggleCreateNewsForm);
    }

    // Fonction pour afficher le formulaire de modification (service ou news)
    window.showEditForm = function(type, id) {
        // Cacher tous les formulaires de modification ouverts
        var forms = document.querySelectorAll('tr[id^="edit-form-"], tr[id^="update-form-"]');
        forms.forEach(function(row) {
            row.style.display = 'none'; // Masquer tous les formulaires
        });

        // Afficher le bon formulaire en fonction du type (service ou news)
        var formRow;
        if (type === 'service') {
            formRow = document.getElementById('edit-form-service-' + id);
        } else if (type === 'news') {
            formRow = document.getElementById('update-form-news-' + id);
        }

        if (formRow) {
            formRow.style.display = 'table-row'; // Afficher le formulaire
            console.log(`Showing ${type} form for ID: ${id}`);
        } else {
            console.error(`Form not found for ${type} ID: ${id}`);
        }
    };

    // Fonction pour cacher le formulaire de modification (service ou news)
    window.hideEditForm = function(type, id) {
        var formRow;
        if (type === 'service') {
            formRow = document.getElementById('edit-form-service-' + id);
        } else if (type === 'news') {
            formRow = document.getElementById('update-form-news-' + id);
        }

        if (formRow) {
            formRow.style.display = 'none';
            console.log(`Hiding ${type} form for ID: ${id}`);
        } else {
            console.error(`Form not found for ${type} ID: ${id}`);
        }
    };

    // Confirmation de suppression
    window.confirmDelete = function() {
        return confirm("Êtes-vous sûr de vouloir supprimer ce service ? Cette action est irréversible.");
    };
});