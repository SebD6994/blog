document.addEventListener('DOMContentLoaded', function() {
    // Déclaration de la fonction confirmDelete en dehors de l'écouteur d'événements
    window.confirmDelete = function() {
        return confirm("Êtes-vous sûr de vouloir supprimer cette image ?");
    }
});