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

    <!-- Section des Patients -->
    <section>
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
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="patientsTableBody">
                <!-- Les résultats seront insérés ici via le JS -->
                <?php
                if (isset($patients) && !empty($patients)) {
                    foreach ($patients as $patient) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($patient['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($patient['phone']) . "</td>";
                        echo "<td>
                                <form action='index.php?page=admin_patient&action=updateRole' method='POST'>
                                    <input type='hidden' name='id' value='" . $patient['id'] . "'>
                                    <select name='role' onchange='this.form.submit()'>
                                        <option value='patient'" . ($patient['role'] === 'patient' ? ' selected' : '') . ">Utilisateur</option>
                                        <option value='admin'" . ($patient['role'] === 'admin' ? ' selected' : '') . ">Administrateur</option>
                                    </select>
                                </form>
                            </td>";
                        echo "<td>
                                <a href='edit_patient.php?id=" . $patient['id'] . "'>Modifier</a>
                                <a href='delete_patient.php?id=" . $patient['id'] . "' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce patient ?\");'>Supprimer</a>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Aucun patient trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

        <!-- Section de Création de Patient -->
        <section>
            <h3>Création de Patient</h3>
            <form action="index.php?page=admin_patient&action=create" method="POST" class="form-style">
                <table>
                    <thead>
                        <tr>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Mot de passe</th>
                            <th>Rôle</th>
                            <th>Actions</th> <!-- Septième colonne pour le bouton -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="first_name" required></td>
                            <td><input type="text" name="last_name" required></td>
                            <td><input type="email" name="email" required></td>
                            <td><input type="text" name="phone" required></td>
                            <td><input type="password" name="password" required></td>
                            <td>
                                <select name="role" required>
                                    <option value="user">Utilisateur</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" class="cta-button">Créer un patient</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </section>

    </main>
    <?php include 'footer.php'; ?>

    <script src="../assets/js/admin_patient.js"></script>
</body>
</html>