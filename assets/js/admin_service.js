function showEditForm(serviceType, serviceId) {
    const editForm = document.getElementById(`edit-form-${serviceType}-${serviceId}`);
    const row = document.getElementById(`row-${serviceId}`);

    row.style.display = 'none';
    editForm.style.display = 'table-row';
}

function hideEditForm(serviceType, serviceId) {
    const editForm = document.getElementById(`edit-form-${serviceType}-${serviceId}`);
    const row = document.getElementById(`row-${serviceId}`);

    editForm.style.display = 'none';
    row.style.display = 'table-row';
}

function toggleCreateServiceForm() {
    const createServiceForm = document.getElementById('create-service-form');
    if (createServiceForm.style.display === 'none' || createServiceForm.style.display === '') {
        createServiceForm.style.display = 'block';
    } else {
        createServiceForm.style.display = 'none';
    }
}

function confirmDelete(message) {
    return confirm(message);
}