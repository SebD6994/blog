function showEditForm(type, id) {
    const editForm = document.getElementById(`edit-form-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    row.style.display = 'none';
    editForm.style.display = 'table-row';
}

function hideEditForm(type, id) {
    const editForm = document.getElementById(`edit-form-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    editForm.style.display = 'none';
    row.style.display = 'table-row';
}

window.confirmDelete = function(message) {
    return confirm(message);
};