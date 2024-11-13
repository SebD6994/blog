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
if (isset($_SESSION['patient']['role'])) {
    if ($_SESSION['patient']['role'] === 'admin') {
        header("Location: index.php?page=admin_appointment");
        exit();
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

                                                <!-- Sélection de la date -->
                                                <input type="date" name="appointment_date" value="<?= htmlspecialchars($dateTime->format('Y-m-d')); ?>" required class="form-input" onchange="updateEditTimeSlots(<?= $appointment['id']; ?>)">

                                                <!-- Sélection de l'heure -->
                                                <select name="time" id="edit-time-slots-<?= htmlspecialchars($appointment['id']); ?>" required>
                                                    <?php if (!empty($timeSlots)): ?>
                                                        <?php foreach ($timeSlots as $slot): ?>
                                                            <option value="<?= htmlspecialchars($slot['slot_start']); ?>" 
                                                                <?= $slot['slot_start'] == $heure ? 'selected' : ''; ?>>
                                                                <?= htmlspecialchars($slot['slot_start']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="">Aucun créneau disponible</option>
                                                    <?php endif; ?>
                                                </select>

                                                <!-- Sélection du service -->
                                                <select name="service_id" required class="form-select">
                                                    <?php foreach ($services as $service): ?>
                                                        <option value="<?= htmlspecialchars($service['id']); ?>" <?= $service['id'] == $appointment['service_id'] ? 'selected' : ''; ?>>
                                                            <?= htmlspecialchars($service['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <!-- Boutons de soumission -->
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
                <select name="appointment_time" id="edit-time-slots-<?= htmlspecialchars($appointment['id']); ?>" required>
                            <?php if (!empty($timeSlots)): ?>
                                <?php foreach ($timeSlots as $slot): ?>
                                    <option value="<?= htmlspecialchars($slot['slot_start']); ?>">
                                        <?= htmlspecialchars($slot['slot_start']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Aucun créneau disponible</option>
                            <?php endif; ?>
                        </select>

                <button type="submit" class="cta-button">Ajouter Rendez-vous</button>
            </form>



        <?php else: ?>
            <section>
                <!-- Bannière -->
                <?php if (isset($bannerImagePath)): ?>
                    <div class="banner">
                        <img src="<?php echo htmlspecialchars($bannerImagePath); ?>" alt="Bannière de la clinique" class="banner-image">
                    </div>
                <?php else: ?>
                    <p>Aucune bannière disponible.</p>
                <?php endif; ?>

                <!-- Services Section -->
                <section class="services">
                    <h3>Nos Services</h3>
                    <ul class="services-list">
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <li><?php echo htmlspecialchars($service['name']); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Aucun service disponible pour le moment.</li>
                        <?php endif; ?>
                    </ul>
                </section>
                <p>Nous vous accueillons avec sourire et professionnalisme dans notre cabinet dentaire.</p>
            <p><a href="index.php?page=patients" class="cta-button">Connectez-vous</a> pour prendre rendez-vous.</p>

            </section>
        <?php endif; ?>
                            
    </main>

    <?php include 'footer.php'; ?>

    <script src="../assets/js/appointment.js"></script>
</body>
</html>
