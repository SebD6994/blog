document.addEventListener('DOMContentLoaded', function() {

    // Écoutez le clic sur le bouton d'ajout d'actualité
const toggleNewsButton = document.getElementById('toggle-create-news-form');
if (toggleNewsButton) {
    toggleNewsButton.addEventListener('click', toggleCreateNewsForm);
}

// Fonction pour afficher le formulaire de création d'actualité
function toggleCreateNewsForm() {
    const createNewsForm = document.getElementById('create-news-form'); // Formulaire d'ajout d'actualité
    const toggleButton = document.getElementById('toggle-create-news-form'); // Bouton pour afficher/masquer le formulaire

    // Bascule l'affichage du formulaire
    createNewsForm.style.display = (createNewsForm.style.display === 'none' || createNewsForm.style.display === '') ? 'block' : 'none';
    toggleButton.textContent = (createNewsForm.style.display === 'block') ? 'Annuler' : 'Ajouter une Actualité'; // Changer le texte du bouton

    // Initialiser TinyMCE sur le textarea du formulaire de création si ce n'est pas déjà fait
    if (createNewsForm.style.display === 'block') {
        if (!tinymce.get('news_content')) { // Vérifier si TinyMCE est déjà initialisé
            initializeTinyMCE('#news_content');
        } else {
            tinymce.get('news_content').focus(); // Mettre le focus sur TinyMCE si déjà initialisé
        }
    } else {
        // Si le formulaire est masqué, détruire l'instance de TinyMCE
        tinymce.get('news_content')?.remove(); // Retirer l'éditeur
    }
}

// Fonction pour afficher le formulaire de modification (service ou news)
window.showEditForm = function(type, id) {
    // Cacher tous les formulaires de modification ouverts
    const forms = document.querySelectorAll('tr[id^="edit-form-"], tr[id^="update-form-"]');
    forms.forEach(row => {
        row.style.display = 'none'; // Masquer tous les formulaires
    });

    // Afficher le bon formulaire en fonction du type (service ou news)
    const formRow = type === 'service' ? document.getElementById(`edit-form-service-${id}`) : document.getElementById(`update-form-news-${id}`);

    if (formRow) {
        formRow.style.display = 'table-row'; // Afficher le formulaire
        console.log(`Showing ${type} form for ID: ${id}`);

        // Initialiser TinyMCE sur le textarea du formulaire de modification si ce n'est pas déjà fait
        if (!tinymce.get(`edit_news_content_${id}`)) { // Vérifier si TinyMCE est déjà initialisé pour ce formulaire
            initializeTinyMCE(`#edit_news_content_${id}`);
        }
    } else {
        console.error(`Form not found for ${type} ID: ${id}`);
    }
};

// Fonction pour cacher le formulaire de modification (service ou news)
window.hideEditForm = function(type, id) {
    const formRow = type === 'service' ? document.getElementById(`edit-form-service-${id}`) : document.getElementById(`update-form-news-${id}`);

    if (formRow) {
        formRow.style.display = 'none';
        console.log(`Hiding ${type} form for ID: ${id}`);
        
        // Détruire l'instance TinyMCE lorsque le formulaire est caché
        tinymce.get(`edit_news_content_${id}`)?.remove(); // Retirer l'éditeur
    } else {
        console.error(`Form not found for ${type} ID: ${id}`);
    }
};

// Fonction pour initialiser TinyMCE
function initializeTinyMCE(selector) {
    tinymce.init({
        selector: selector, // Utilisation du sélecteur passé en argument
        plugins: ['link', 'image', 'lists', 'table', 'textcolor', 'emoticons'], // Plugins courants
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright | bullist numlist | link image | forecolor backcolor | emoticons', // Toolbar avec options courantes
        menubar: false, // Pas de menu
        toolbar_sticky: true, // Toolbar fixe en haut
        image_advtab: true, // Ouvrir les options avancées pour les images
        automatic_uploads: true, // Activer le téléchargement automatique
        images_upload_url: '../assets/images/news/', // Endpoint pour l'upload d'images
        images_upload_handler: function (blobInfo, success, failure) {
            // Utiliser FileReader pour lire le fichier local
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '../assets/images/news/'); // Endpoint pour gérer l'upload

            xhr.onload = function() {
                if (xhr.status === 403) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }

                const json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location !== 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                success(json.location); // Renvoie l'URL de l'image insérée
            };

            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename()); // Ajouter le fichier à FormData

            xhr.send(formData); // Envoyer le formulaire
        },
        file_picker_callback: function(callback, value, meta) {
            if (meta.filetype === 'image') {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*'); // Accepter uniquement les fichiers image

                input.onchange = function() {
                    const file = this.files[0];
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Lorsque le fichier est chargé, appeler le callback pour insérer l'image
                        callback(e.target.result, {
                            alt: file.name
                        });
                    };
                    reader.readAsDataURL(file); // Lire le fichier comme URL de données
                };

                input.click(); // Ouvrir le sélecteur de fichiers
            }
        },
        branding: false, // Ne pas afficher le logo TinyMCE
        content_css: '//www.tiny.cloud/css/content.min.css', // CSS de contenu
        init_instance_callback: function(editor) {
            // Code à exécuter lorsque l'éditeur est initialisé
            console.log('TinyMCE initialized for', editor.id);
        }
    });
}

// Forcer la mise à jour du contenu de TinyMCE avant la soumission du formulaire
document.getElementById('create-news-form')?.addEventListener('submit', function(e) {
    tinymce.triggerSave(); // Forcer TinyMCE à mettre à jour le contenu
    console.log('Submitting news form with TinyMCE content');
});

    // Confirmation de suppression
    window.confirmDelete = function(message) {
        return confirm(message); // Affiche une boîte de confirmation
    };
});