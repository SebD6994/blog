document.addEventListener('DOMContentLoaded', function() {
    var editButton = document.getElementById('editButton');
    var editForm = document.getElementById('editForm');
    var patientInfo = document.querySelector('.patient-info');  // Sélectionner la première div
    var cancelButton = document.getElementById('cancelButton');
    
    editButton.addEventListener('click', function() {
        patientInfo.style.display = 'none';
        editForm.style.display = 'block';
    });
    
    cancelButton.addEventListener('click', function(event) {
        event.preventDefault();
        patientInfo.style.display = 'block';
        editForm.style.display = 'none';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const showRegisterFormLink = document.getElementById('show-register-form');
    const registerForm = document.getElementById('register-form');
    const loginSection = document.querySelector('.login-section');

    if (showRegisterFormLink && registerForm && loginSection) {
        showRegisterFormLink.addEventListener('click', function(event) {
            event.preventDefault();
            loginSection.style.display = 'none';
            registerForm.style.display = 'block';
        });
    }

    const showLoginFormLink = document.getElementById('show-login-form');

    if (showLoginFormLink) {
        showLoginFormLink.addEventListener('click', function(event) {
            event.preventDefault();
            registerForm.style.display = 'none';
            loginSection.style.display = 'block';
        });
    }
});