// Attendre que le DOM soit complètement chargé avant d'ajouter des événements
document.addEventListener('DOMContentLoaded', function() {
    var editButton = document.getElementById('editButton');
    var editForm = document.getElementById('editForm');
    var patientInfo = document.querySelector('.patient-info');
    var cancelButton = document.getElementById('cancelButton'); // Bouton Annuler
    
    // Ajouter un écouteur d'événement au bouton "Modifier mes informations"
    editButton.addEventListener('click', function() {
        // Masquer les informations actuelles du patient
        patientInfo.style.display = 'none';
        
        // Afficher le formulaire de modification
        editForm.style.display = 'block';
    });
    
    // Ajouter un écouteur d'événement au bouton "Annuler"
    cancelButton.addEventListener('click', function(event) {
        event.preventDefault(); // Empêcher le comportement par défaut du lien
        
        // Réafficher les informations du patient
        patientInfo.style.display = 'block';
        
        // Cacher le formulaire de modification
        editForm.style.display = 'none';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const showRegisterFormLink = document.getElementById('show-register-form');
    const registerForm = document.getElementById('register-form');
    const loginSection = document.querySelector('.login-section'); // Sélectionnez la section de connexion

    if (showRegisterFormLink && registerForm && loginSection) {
        showRegisterFormLink.addEventListener('click', function(event) {
            event.preventDefault();
            loginSection.style.display = 'none'; // Masquer toute la section de connexion
            registerForm.style.display = 'block'; // Afficher le formulaire d'inscription
        });
    }

    const showLoginFormLink = document.getElementById('show-login-form');

    if (showLoginFormLink) {
        showLoginFormLink.addEventListener('click', function(event) {
            event.preventDefault();
            registerForm.style.display = 'none'; // Masquer le formulaire d'inscription
            loginSection.style.display = 'block'; // Afficher la section de connexion
        });
    }
});