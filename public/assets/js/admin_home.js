document.addEventListener('DOMContentLoaded', function() {
    window.confirmDelete = function() {
        return confirm("Êtes-vous sûr de vouloir supprimer cette image ?");
    }
});