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
    <title>Rendez-vous</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <?php include 'header.php'; ?>
    <main>

    <!-- Section des Rendez-vous -->
    <section class="sections-container" style="display: block;">
    <h2>Liste des Rendez-vous pour aujourd'hui</h2>
    <?php 
    // Récupérer la date d'aujourd'hui
    $today = date('Y-m-d');

    // Récupérer tous les rendez-vous pour aujourd'hui
    $appointmentsToday = array_filter($appointments, function($appointment) use ($today) {
        $dateTime = new DateTime($appointment['appointment_date']);
        return $dateTime->format('Y-m-d') === $today;
    });
    ?>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Service</th>
                <th>Patient</th>
                <th>Action</th>
            </tr>
        </thead>
            <tbody id="appointmentsTableBody">
        <?php if (empty($appointmentsToday)): ?>
            <tr>
                <td colspan="5">Aucun rendez-vous trouvé pour aujourd'hui.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($appointmentsToday as $appointment): ?>
                <!-- Ligne avec les informations de base -->
                <tr id="row-appointment-<?= $appointment['id']; ?>">
                    <td><?= htmlspecialchars(date('d/m/Y')); ?></td> <!-- Date d'aujourd'hui -->
                    <td>
                        <?php 
                        // Affiche l'heure du rendez-vous
                        $dateTime = new DateTime($appointment['appointment_date']);
                        echo htmlspecialchars($dateTime->format('H:i'));
                        ?>
                    </td>
                    <td><?= htmlspecialchars($appointment['service_name'] ?? 'Service non spécifié'); ?></td> <!-- Service -->
                    <td><?= !empty($appointment['patient_name']) ? htmlspecialchars($appointment['patient_name']) : '-'; ?></td> <!-- Patient -->
                    <td>
                        <button type="button" class="button" onclick="showEditForm('appointment', <?= $appointment['id']; ?>)">Modifier</button>
                        
                        <!-- Formulaire de suppression -->
                        <form action="index.php?page=admin_appointment&action=delete" method="post" style="display:block;" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?');">
                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']); ?>">
                            <button type="submit" class="delete-button">Supprimer</button>
                        </form>
                    </td>
                </tr>

                <!-- Formulaire de modification caché -->
                <tr id="edit-row-appointment-<?= $appointment['id']; ?>" style="display:none;">
                    <td>
                        <!-- Sélection de la date -->
                        <input type="date" name="appointment_date" value="<?= htmlspecialchars((new DateTime($appointment['appointment_date']))->format('Y-m-d')); ?>" required style="width: 100%;" onchange="updateEditTimeSlots(<?= $appointment['id']; ?>)">
                    </td>
                    <td>
                        <select name="time" id="edit-time-slots-<?= htmlspecialchars($appointment['id']); ?>" required style="width: 100%;">
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
                    </td>
                    <td>
                        <select name="service_id" id="service_id" required style="width: 100%;">
                            <option value="">Service</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= htmlspecialchars($service['id']); ?>"><?= htmlspecialchars($service['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><?= !empty($appointment['patient_name']) ? htmlspecialchars($appointment['patient_name']) : '-'; ?></td>
                    <td>
                        <button type="submit" class="button">Enregistrer</button>
                        <button type="button" class="button" onclick="hideEditForm('appointment', <?= $appointment['id']; ?>)">Annuler</button>
                    </td>
                        </form>
                </tr>

            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>


    </table>
</section>
<section class="sections-container" style="display: block;">
    <!-- Formulaire de création de rendez-vous dans un tableau distinct -->
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

        <!-- Formulaire de création dans un tableau -->
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Patient</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <form action="index.php?page=appointments&action=create" method="POST" class="form-style">
                        <!-- Service -->
                        <td>
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
                        </td>

                        <!-- Date -->
                        <td>
                            <?php 
                                // Obtenir la date d'aujourd'hui au format 'Y-m-d'
                                $today = date('Y-m-d');
                            ?>
                            <input type="date" name="appointment_date" id="appointment_date" required onchange="updateTimeSlots()" min="<?= $today; ?>">
                        </td>

                        <!-- Heure -->
                        <td>
                            <select name="appointment_time" id="edit-time-slots" required>
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
                        </td>

                        <!-- Patient -->
                        <td>
                            <select name="patient_id" id="patient_id" required>
                                <option value="">Patient</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?= htmlspecialchars($patient['id']); ?>">
                                        <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?> <!-- Concaténer first_name et last_name -->
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>

                        <!-- Actions (soumission) -->
                        <td>
                            <button type="submit" class="button">Ajouter</button>
                        </td>
                    </form>
                </tr>
            </tbody>
        </table>
</section>

<section class="sections-container" style="display: block;">
    <h2>Liste des Rendez-vous à venir</h2>

    <?php 
    // Récupérer la date d'aujourd'hui
    $today = date('Y-m-d');

    // Récupérer tous les rendez-vous futurs
    $futureAppointments = array_filter($appointments, function($appointment) use ($today) {
        $dateTime = new DateTime($appointment['appointment_date']);
        return $dateTime->format('Y-m-d') > $today;
    });
    ?>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Service</th>
                <th>Patient</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($futureAppointments)): ?>
                <tr>
                    <td colspan="5">Aucun rendez-vous futur trouvé.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($futureAppointments as $appointment): ?>
                    <tr id="row-appointment-<?= htmlspecialchars($appointment['id']); ?>">
                        <td>
                            <?php 
                            // Affiche la date du rendez-vous
                            $dateTime = new DateTime($appointment['appointment_date']);
                            echo htmlspecialchars($dateTime->format('d/m/Y'));
                            ?>
                        </td>
                        <td>
                            <?php 
                            // Affiche l'heure du rendez-vous
                            echo htmlspecialchars($dateTime->format('H:i'));
                            ?>
                        </td>
                        <td><?= htmlspecialchars($appointment['service_name'] ?? 'Service non spécifié'); ?></td>
                        <td><?= !empty($appointment['patient_name']) ? htmlspecialchars($appointment['patient_name']) : '-'; ?></td>
                        <td>
                            <!-- Bouton pour afficher le formulaire de modification -->
                            <button type="button" class="button" onclick="showEditForm('appointment', <?= htmlspecialchars($appointment['id']); ?>)">Modifier</button>
                            <form action="index.php?page=admin_appointment&action=delete" method="post" style="display:block;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');">
                                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['id']); ?>">
                                <button type="submit" class="delete-button">Supprimer</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Formulaire de modification caché -->
                <tr id="edit-row-appointment-<?= $appointment['id']; ?>" style="display:none;">
                    <td>
                        <!-- Sélection de la date -->
                        <input type="date" name="appointment_date" value="<?= htmlspecialchars((new DateTime($appointment['appointment_date']))->format('Y-m-d')); ?>" required style="width: 100%;" onchange="updateEditTimeSlots(<?= $appointment['id']; ?>)">
                    </td>
                    <td>
                        <select name="time" id="edit-time-slots-<?= htmlspecialchars($appointment['id']); ?>" required style="width: 100%;">
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
                    </td>
                    <td>
                        <select name="service_id" id="service_id" required style="width: 100%;">
                            <option value="">Service</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= htmlspecialchars($service['id']); ?>"><?= htmlspecialchars($service['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><?= !empty($appointment['patient_name']) ? htmlspecialchars($appointment['patient_name']) : '-'; ?></td>
                    <td>
                        <button type="submit" class="button">Enregistrer</button>
                        <button type="button" class="button" onclick="hideEditForm('appointment', <?= $appointment['id']; ?>)">Annuler</button>
                    </td>
                        </form>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

    </main>
    <?php include 'footer.php'; ?>

    <script src="../assets/js/admin_appointment.js"></script>
</body>
</html>