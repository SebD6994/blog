document.addEventListener('DOMContentLoaded', function() {
    // Confirmation de suppression
    window.confirmDelete = function(message) {
        return confirm(message); // Affiche une boîte de confirmation
    };
});