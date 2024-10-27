function showEditForm(type, id) {
    const editForm = document.getElementById(`edit-form-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    row.style.display = 'none';
    editForm.style.display = 'table-row';

    const dateInput = document.getElementById(`edit-date-${id}`);
    dateInput.addEventListener('change', function() {
        updateAvailableTimeSlots(id, this.value);
    });
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