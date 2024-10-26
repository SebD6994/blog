<?php if (isset($_SESSION['success'])): ?>
    <div class="success-message">
        <?= htmlspecialchars($_SESSION['success']); ?>
        <?php unset($_SESSION['success']); // Effacer le message après l'affichage ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-message">
        <?= htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); // Effacer le message après l'affichage ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Patients</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <?php include 'header.php'; ?>
    <main>
        <!-- Section des patients -->
        <section class="sections-container" style="display: block;">
            <h2 class="section-title">Gestion des Patients</h2>

            <!-- Formulaire de recherche de patients -->
            <form id="searchForm" method="GET" action="index.php" class="form-style">
                <input type="hidden" name="page" value="admin_patient">
                <input type="hidden" name="action" value="searchPatients">
                <input type="text" name="search" id="searchInput" placeholder="Rechercher par prénom, nom, email ou téléphone">
                <button type="submit">Rechercher</button>
            </form>

            <h3>Liste des Patients</h3>
            <table>
    <thead>
        <tr>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="patientsTableBody">
    <?php
    if (isset($patients) && !empty($patients)) {
        foreach ($patients as $patient) {
            // Ligne du patient
            echo "<tr id='row-patient-" . htmlspecialchars($patient['id']) . "'>";
            echo "<td>" . htmlspecialchars($patient['first_name']) . "</td>";
            echo "<td>" . htmlspecialchars($patient['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($patient['email']) . "</td>";
            echo "<td>" . htmlspecialchars($patient['phone']) . "</td>";
            echo "<td>
                    <button type='button' class='button' onclick=\"showEditForm('patient', " . htmlspecialchars($patient['id']) . ")\">Modifier</button>
                    <form action='index.php?page=admin_patient&action=delete' method='post' style='display:inline;' onsubmit='return confirmDelete(\"Êtes-vous sûr de vouloir supprimer ce patient ?\");'>
                        <input type='hidden' name='patient_id' value='" . htmlspecialchars($patient['id']) . "'>
                        <button type='submit' class='delete-button'>Supprimer</button>
                    </form>
                </td>";
            echo "</tr>";
            
            // Formulaire de modification caché pour chaque patient
echo "<tr id='edit-form-patient-" . htmlspecialchars($patient['id']) . "' style='display: none;'>";
echo "<td>";
echo "<form action='index.php?page=admin_patient&action=update&id=" . htmlspecialchars($patient['id']) . "' method='post'>";
echo "<input type='hidden' name='id' value='" . htmlspecialchars($patient['id']) . "'>";
echo "<input type='text' name='first_name' value='" . htmlspecialchars($patient['first_name']) . "' required style='width: 100%;'>";
echo "</td>";
echo "<td>";
echo "<input type='text' name='last_name' value='" . htmlspecialchars($patient['last_name']) . "' required style='width: 100%;'>";
echo "</td>";
echo "<td>";
echo "<input type='email' name='email' value='" . htmlspecialchars($patient['email']) . "' required style='width: 100%;'>";
echo "</td>";
echo "<td>";
echo "<input type='text' name='phone' value='" . htmlspecialchars($patient['phone']) . "' required style='width: 100%;'>";
echo "</td>";
echo "<td>
        <button type='submit'>Enregistrer</button>
        <button type='button' onclick=\"hideEditForm('patient', " . htmlspecialchars($patient['id']) . ")\">Annuler</button>
      </form>
    </td>";
echo "</tr>";

        }
    } else {
        echo "<tr><td colspan='5'>Aucun patient trouvé.</td></tr>"; // Ajustement pour le nombre de colonnes
    }
    ?>
    </tbody>
</table>

        </section>



        <!-- Formulaire de création de patient dans un tableau distinct -->
        <h2>Créer un Nouveau Patient</h2>
        <form action="index.php?page=admin_patient&action=create" method="POST">
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Mot de passe</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" id="first_name" name="first_name" required></td>
                        <td><input type="text" id="last_name" name="last_name" required></td>
                        <td><input type="email" id="email" name="email" required></td>
                        <td><input type="text" id="phone" name="phone" required></td>
                        <td>
                            <input type="password" id="password" name="password" required>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </td>
                        <td>
                            <select name="role" required>
                                <option value="user">Utilisateur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" class="button">Ajouter</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        
    </main>

    <?php include 'footer.php'; ?>

    <script src="../assets/js/admin_patient.js"></script>
</body>
</html>