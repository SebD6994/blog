document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche le rechargement de la page

    const searchValue = document.getElementById('searchInput').value;

    // Vérifie si un terme de recherche a été entré
    if (searchValue.trim() !== '') {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `index.php?page=admin_patient&action=searchPatients&search=${encodeURIComponent(searchValue)}`, true);

        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (xhr.status === 200) {
                const patients = JSON.parse(xhr.responseText);

                const tbody = document.getElementById('patientsTableBody');
                tbody.innerHTML = ''; // Vide le tableau avant d'insérer les nouveaux résultats

                if (patients.length > 0) {
                    patients.forEach(function(patient) {
                        const row = `<tr>
                            <td>${patient.first_name}</td>
                            <td>${patient.last_name}</td>
                            <td>${patient.email}</td>
                            <td>${patient.phone}</td>
                            <td>
                                <form action='index.php?page=admin_patient&action=updateRole' method='POST'>
                                    <input type='hidden' name='id' value='${patient.id}'>
                                    <select name='role' onchange='this.form.submit()'>
                                        <option value='patient' ${patient.role === 'patient' ? 'selected' : ''}>Utilisateur</option>
                                        <option value='admin' ${patient.role === 'admin' ? 'selected' : ''}>Administrateur</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href='edit_patient.php?id=${patient.id}'>Modifier</a>
                                <a href='delete_patient.php?id=${patient.id}' onclick='return confirm("Êtes-vous sûr de vouloir supprimer ce patient ?");'>Supprimer</a>
                            </td>
                        </tr>`;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6">Aucun patient trouvé.</td></tr>';
                }
            } else {
                console.error('Erreur dans la requête AJAX');
            }
        };

        xhr.send();
    }
});

// Fonction pour afficher le formulaire de modification d'un patient spécifique
function showEditForm(type, id) {
    const editForm = document.getElementById(`edit-form-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    // Masquer la ligne du patient
    row.style.display = 'none';
    
    // Afficher le formulaire de modification
    editForm.style.display = 'table-row'; // Affichage en tant que ligne de tableau
}

// Fonction pour masquer le formulaire de modification d'un patient spécifique
function hideEditForm(type, id) {
    const editForm = document.getElementById(`edit-form-${type}-${id}`);
    const row = document.getElementById(`row-${type}-${id}`);

    // Masquer le formulaire de modification
    editForm.style.display = 'none';
    
    // Afficher à nouveau la ligne du patient
    row.style.display = 'table-row'; // Affichage en tant que ligne de tableau
}



// Fonction pour la confirmation de suppression
window.confirmDelete = function(message) {
    return confirm(message); // Affiche une boîte de confirmation
};
