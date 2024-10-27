document.addEventListener('DOMContentLoaded', function() {
    function initializeTinyMCE(selector) {
        tinymce.init({
            selector: selector,
            plugins: ['link', 'image', 'lists', 'table', 'textcolor', 'emoticons'],
            toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright | bullist numlist | link image | forecolor backcolor | emoticons',
            menubar: false,
            toolbar_sticky: true,
            image_advtab: true,
            automatic_uploads: true,
            images_upload_url: '../assets/images/news/',
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
                    success(json.location);
                };

                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            },
            file_picker_callback: function(callback, value, meta) {
                if (meta.filetype === 'image') {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.onchange = function() {
                        const file = this.files[0];
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            callback(e.target.result, { alt: file.name });
                        };
                        reader.readAsDataURL(file);
                    };
                    input.click();
                }
            },
            branding: false,
            content_css: '//www.tiny.cloud/css/content.min.css',
            init_instance_callback: function(editor) {
                console.log('TinyMCE initialized for', editor.id);
            }
        });
    }

    initializeTinyMCE('textarea');

    document.getElementById('create-news-form')?.addEventListener('submit', function(e) {
        tinymce.triggerSave();
        console.log('Submitting news form with TinyMCE content');
    });

    window.showEditForm = function(id) {
        const forms = document.querySelectorAll('tr[id^="update-form-news-"]');
        forms.forEach(row => {
            row.style.display = 'none';
        });

        const formRow = document.getElementById(`update-form-news-${id}`);
        if (formRow) {
            formRow.style.display = 'table-row';
            console.log(`Showing news form for ID: ${id}`);
            initializeTinyMCE(`#edit_news_content_${id}`);
        } else {
            console.error(`Form not found for news ID: ${id}`);
        }
    };

    window.hideEditForm = function(id) {
        const formRow = document.getElementById(`update-form-news-${id}`);
        if (formRow) {
            formRow.style.display = 'none';
            console.log(`Hiding news form for ID: ${id}`);
            tinymce.get(`edit_news_content_${id}`)?.remove();
        } else {
            console.error(`Form not found for news ID: ${id}`);
        }
    };

    window.confirmDelete = function(message) {
        return confirm(message);
    };
});