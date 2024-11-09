<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['success'])): ?>
    <div class="success-message">
        <?= htmlspecialchars($_SESSION['success']); ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="error-message">
        <?= htmlspecialchars($_SESSION['error']); ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php 
if (!isset($_SESSION['patient']['role']) || $_SESSION['patient']['role'] !== 'admin') {
    header("Location: index.php?page=patients");
    exit();
}
?>


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
            <h2>Gestion des Patients</h2>

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
                    <?php if (isset($patients) && !empty($patients)): ?>
                        <?php foreach ($patients as $patient): ?>
                            <!-- Ligne du patient -->
                            <tr id="row-patient-<?= htmlspecialchars($patient['id']); ?>">
                                <td><?= htmlspecialchars($patient['first_name']); ?></td>
                                <td><?= htmlspecialchars($patient['last_name']); ?></td>
                                <td><?= htmlspecialchars($patient['email']); ?></td>
                                <td><?= htmlspecialchars($patient['phone']); ?></td>
                                <td>
                                    <button type="button" class="button" onclick="showEditForm('patient', <?= htmlspecialchars($patient['id']); ?>)">Modifier</button>
                                    <form action="index.php?page=admin_patient&action=delete" method="post" style="display:block;" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer ce patient ?');">
                                        <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient['id']); ?>">
                                        <button type="submit" class="delete-button">Supprimer</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Formulaire de modification caché pour chaque patient -->
                            <tr id="edit-form-patient-<?= htmlspecialchars($patient['id']); ?>" style="display: none;">
                                <td>
                                    <form action="index.php?page=admin_patient&action=update&id=<?= htmlspecialchars($patient['id']); ?>" method="post">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($patient['id']); ?>">
                                        <input type="text" name="first_name" value="<?= htmlspecialchars($patient['first_name']); ?>" required style="width: 100%;">
                                </td>
                                <td>
                                        <input type="text" name="last_name" value="<?= htmlspecialchars($patient['last_name']); ?>" required style="width: 100%;">
                                </td>
                                <td>
                                        <input type="email" name="email" value="<?= htmlspecialchars($patient['email']); ?>" required style="width: 100%;">
                                </td>
                                <td>
                                        <input type="text" name="phone" value="<?= htmlspecialchars($patient['phone']); ?>" required style="width: 100%;">
                                </td>
                                <td>
                                        <button type="submit" class="button">Enregistrer</button>
                                        <button type="button" class="button" onclick="hideEditForm('patient', <?= htmlspecialchars($patient['id']); ?>)">Annuler</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Aucun patient trouvé.</td></tr>
                    <?php endif; ?>
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
                        <td><input type="text" id="first_name" name="first_name" placeholder="Nom" required></td>
                        <td><input type="text" id="last_name" name="last_name" placeholder="Prénom" required></td>
                        <td><input type="email" id="email" name="email" placeholder="Email" required></td>
                        <td><input type="text" id="phone" name="phone" placeholder="Téléphone" required></td>
                        <td>
                            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmation" required>
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