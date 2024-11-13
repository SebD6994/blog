document.addEventListener('DOMContentLoaded', function() {


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