// Fonction pour afficher le formulaire de modification
function showEditForm(type, id) {
    const editForm = document.getElementById(`edit-row-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    // Masquer la ligne normale et afficher le formulaire d'édition
    row.style.display = 'none';
    editForm.style.display = 'table-row';
}

// Fonction pour masquer le formulaire de modification
function hideEditForm(type, id) {
    const editForm = document.getElementById(`edit-row-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    // Masquer le formulaire d'édition et afficher la ligne normale
    editForm.style.display = 'none';
    row.style.display = 'table-row';
}

// Fonction de confirmation avant suppression
window.confirmDelete = function(message) {
    return confirm(message); // Affiche une boîte de confirmation avant suppression
};
