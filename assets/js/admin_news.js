document.addEventListener('DOMContentLoaded', function() {
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
                const xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '../assets/images/news/');

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
                formData.append('file', blobInfo.blob(), blobInfo.filename());
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
                            callback(e.target.result, { alt: file.name });
                        };
                        reader.readAsDataURL(file); // Lire le fichier comme URL de données
                    };
                    input.click(); // Ouvrir le sélecteur de fichiers
                }
            },
            branding: false, // Ne pas afficher le logo TinyMCE
            content_css: '//www.tiny.cloud/css/content.min.css', // CSS de contenu
            init_instance_callback: function(editor) {
                console.log('TinyMCE initialized for', editor.id);
            }
        });
    }

    // Initialiser TinyMCE pour toutes les zones de texte, y compris celles des formulaires de modification
    initializeTinyMCE('textarea'); // Cela va initialiser TinyMCE pour tous les textarea sur la page

    // Forcer la mise à jour du contenu de TinyMCE avant la soumission du formulaire de création
    document.getElementById('create-news-form')?.addEventListener('submit', function(e) {
        tinymce.triggerSave(); // Forcer TinyMCE à mettre à jour le contenu
        console.log('Submitting news form with TinyMCE content');
    });

    // Fonction pour afficher le formulaire de modification d'une news
    window.showEditForm = function(id) {
        // Cacher tous les formulaires de modification ouverts
        const forms = document.querySelectorAll('tr[id^="update-form-news-"]');
        forms.forEach(row => {
            row.style.display = 'none'; // Masquer tous les formulaires
        });

        // Afficher le bon formulaire en fonction de l'ID
        const formRow = document.getElementById(`update-form-news-${id}`);
        if (formRow) {
            formRow.style.display = 'table-row'; // Afficher le formulaire
            console.log(`Showing news form for ID: ${id}`);

            // Initialiser TinyMCE sur le textarea du formulaire de modification
            initializeTinyMCE(`#edit_news_content_${id}`);
        } else {
            console.error(`Form not found for news ID: ${id}`);
        }
    };

    // Fonction pour cacher le formulaire de modification d'une news
    window.hideEditForm = function(id) {
        const formRow = document.getElementById(`update-form-news-${id}`);
        if (formRow) {
            formRow.style.display = 'none';
            console.log(`Hiding news form for ID: ${id}`);
            // Détruire l'instance TinyMCE lorsque le formulaire est caché
            tinymce.get(`edit_news_content_${id}`)?.remove();
        } else {
            console.error(`Form not found for news ID: ${id}`);
        }
    };

    // Confirmation de suppression
    window.confirmDelete = function(message) {
        return confirm(message);
    };
});
