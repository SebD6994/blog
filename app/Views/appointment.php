<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification si l'utilisateur est connecté et son rôle
if (isset($_SESSION['patient']['role'])) {
    if ($_SESSION['patient']['role'] === 'admin') {
        // Redirection vers la page admin
        header("Location: index.php?page=admin_appointment");
        exit();
    } elseif ($_SESSION['patient']['role'] === 'user') {
        // Pas de redirection pour l'utilisateur avec le rôle 'user'
        // Vous pouvez ajouter un message ou un traitement spécifique ici si nécessaire
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rendez-vous</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main>

    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php elseif (isset($successMessage)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>
        <?php if ($patientData): ?>
            <h2>Mes Rendez-vous</h2>
            <div class="sections-container">
                <section class="appointments-section">
                    <?php if (!empty($appointments)): ?>
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Service</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                    <?php 
                                        $dateTime = new DateTime($appointment['appointment_date']);
                                        $date = $dateTime->format('d/m/Y');
                                        $heure = $dateTime->format('H:i');
                                    ?>
                                    <tr id="row-<?= $appointment['id']; ?>">
                                        <td><?= htmlspecialchars($date); ?></td>
                                        <td><?= htmlspecialchars($heure); ?></td>
                                        <td><?= htmlspecialchars($appointment['service_name'] ?? 'Service non spécifié'); ?></td>
                                        <td>
                                            <button class="button" onclick="showEditForm(<?= $appointment['id']; ?>)">Modifier</button>
                                            <form action="index.php?page=appointments&action=delete" method="post" style="display:inline;" onsubmit="return confirmDelete();">
                                                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']); ?>">
                                                <button type="submit" class="button delete-button">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr id="edit-row-<?= $appointment['id']; ?>" style="display:none;">
                                        <td colspan="4">
                                            <form id="edit-form-<?= $appointment['id']; ?>" action="index.php?page=appointments&action=update" method="post">
                                                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']); ?>">

                                                <input type="date" name="appointment_date" value="<?= htmlspecialchars($dateTime->format('Y-m-d')); ?>" required class="form-input" onchange="updateEditTimeSlots(<?= $appointment['id']; ?>)">
                                                <input type="time" name="appointment_time" value="<?= htmlspecialchars($dateTime->format('H:i')); ?>" required class="form-input">

                                                <select name="service_id" required class="form-select">
                                                    <?php foreach ($services as $service): ?>
                                                        <option value="<?= htmlspecialchars($service['id']); ?>" <?= $service['id'] == $appointment['service_id'] ? 'selected' : ''; ?>>
                                                            <?= htmlspecialchars($service['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <button type="submit" class="button">Enregistrer</button>
                                                <button type="button" class="button" onclick="hideEditForm(<?= $appointment['id']; ?>)">Annuler</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Aucun rendez-vous trouvé.</p>
                    <?php endif; ?>
                </section>
            </div>

            <h2>Ajouter un Rendez-vous</h2>

            <!-- Message d'erreur -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error">
                    <?php 
                        echo $_SESSION['error_message']; 
                        unset($_SESSION['error_message']); // Supprimer le message après l'affichage
                    ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=appointments&action=create" method="POST" class="form-style">                    
                <label for="service_id">Service</label>
                <select name="service_id" id="service_id" required>
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo htmlspecialchars($service['id']); ?>">
                                <?php echo htmlspecialchars($service['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">Aucun service disponible</option>
                    <?php endif; ?>
                </select>

                <label for="appointment_date">Date</label>
                <?php 
                    // Obtenir la date d'aujourd'hui au format 'Y-m-d'
                    $today = date('Y-m-d');
                ?>
                <input type="date" name="appointment_date" id="appointment_date" required onchange="updateTimeSlots()" min="<?= $today; ?>">

                <label for="appointment_time">Heure</label>
                <select name="appointment_time" id="appointment_time" required>
                    <option value="" disabled selected>Sélectionner une heure</option>
                    <!-- Les options seront remplies dynamiquement en fonction de la date sélectionnée -->
                </select>

                <button type="submit" class="cta-button">Ajouter Rendez-vous</button>
            </form>

        <?php else: ?>
            <p><a href="index.php?page=patients" class="cta-button">Connectez-vous</a> pour prendre rendez-vous.</p>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>

    <script src="../assets/js/appointment.js"></script>
</body>
</html>
