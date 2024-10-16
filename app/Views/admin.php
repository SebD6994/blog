<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <script src="../assets/js/calendar.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
</head>

<?php include 'header.php'; ?>

<body>
    <main>
        <h1>Tableau de bord de l'Admin</h1>

        <!-- Section des Rendez-vous -->
        <h2 class="section-title">Rendez-vous</h2>
        <section class="sections-container" style="display: none;">
            <h2>Liste des Rendez-vous pour aujourd'hui</h2>

            <?php 
            // Récupérer la date d'aujourd'hui
            $today = date('Y-m-d');

            // Regrouper les rendez-vous pour aujourd'hui
            $appointmentsToday = array_filter($appointments, function($appointment) use ($today) {
                $dateTime = new DateTime($appointment['appointment_date']);
                return $dateTime->format('Y-m-d') === $today;
            });
            ?>

            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Service</th>
                        <th>Patient</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($availableSlots as $slot): 
                        $isBooked = false;
                        $associatedAppointment = null;

                        // Vérifier si le créneau est réservé
                        foreach ($appointmentsToday as $appointment): 
                            $appointmentDateTime = new DateTime($appointment['appointment_date']);
                            if ($appointmentDateTime->format('H:i') === $slot): 
                                $isBooked = true; 
                                $associatedAppointment = $appointment; // Stocker le rendez-vous associé
                                break; 
                            endif; 
                        endforeach; 
                    ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d/m/Y')); ?></td>
                            <td><?= htmlspecialchars($slot); ?></td>
                            <?php if ($isBooked): ?>
                                <td><?= htmlspecialchars($associatedAppointment['service_name'] ?? 'Service non spécifié'); ?></td>
                                <td>
                                    <?= !empty($associatedAppointment['patient_name']) ? htmlspecialchars($associatedAppointment['patient_name']) : '-'; ?>
                                </td>
                                <td>
                                    <form action="index.php?page=admin&action=updateAppointment" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($associatedAppointment['id']); ?>">
                                        <select name="status" required>
                                            <option value="pending" <?= $associatedAppointment['status'] === 'pending' ? 'selected' : ''; ?>>En attente</option>
                                            <option value="confirmed" <?= $associatedAppointment['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmé</option>
                                            <option value="cancelled" <?= $associatedAppointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Annulé</option>
                                        </select>
                                        <button type="submit" class="button">Mettre à jour</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="index.php?page=appointments&action=delete" method="post" style="display:inline;" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($associatedAppointment['id']); ?>">
                                        <button type="submit" class="delete-button">Supprimer</button>
                                    </form>
                                </td>
                            <?php else: ?>
                                <td>-</td> <!-- Service non réservé -->
                                <td>-</td> <!-- Patient non associé -->
                                <td>Disponible</td> <!-- Indication de disponibilité -->
                                <td>
                                    <button class="button" onclick="showBookingForm('<?= htmlspecialchars($slot); ?>')">Réserver</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (empty($appointmentsToday)): ?>
                <p>Aucun rendez-vous trouvé pour aujourd'hui.</p>
            <?php endif; ?>
            
            <a href="index.php?page=appointments" class="cta-button">Créer un Rendez-vous</a>
        </section>



<!-- Section des Patients -->
        <section>
            <h2 class="section-title">Patients</h2>
            <div class="sections-container" style="display: none;">
                <h3>Recherche de Patients</h3>
                <form method="GET" action="index.php" class="form-style">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="action" value="searchPatients">
                    <input type="text" name="search" placeholder="Rechercher par prénom, nom, email ou téléphone" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Rechercher</button>
                </form>

                <?php
                // Afficher la table des patients uniquement si une recherche a été effectuée
                if (isset($_GET['action']) && $_GET['action'] === 'searchPatients') {
                    if (isset($patients) && !empty($patients)) {
                        echo '<h3>Résultats de la recherche :</h3>';
                        echo '<table>';
                        echo '<thead>
                                <tr>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>';
                        echo '<tbody>';
                        
                        foreach ($patients as $patient) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($patient['first_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($patient['last_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($patient['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($patient['phone']) . "</td>";
                            echo "<td>
                                    <form action='index.php?page=admin&action=updateRole' method='POST'>
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
                        
                        echo '</tbody></table>';
                    } else {
                        echo "<p>Aucun patient trouvé pour votre recherche.</p>";
                    }
                }
                ?>

                <h3>Création de Patient</h3>
                <form action="index.php?page=patients&action=create" method="POST" class="form-style">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Prénom :</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Nom :</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Téléphone :</label>
                            <input type="text" id="phone" name="phone" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Mot de passe :</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirmer le mot de passe :</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">Rôle :</label>
                            <select id="role" name="role" required>
                                <option value="user">Utilisateur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                    </div>

                    <?php if (isset($registrationError)): ?>
                        <p style="color: red;"><?= htmlspecialchars($registrationError); ?></p>
                    <?php endif; ?>

                    <button type="submit" class="cta-button">Créer un patient</button>
                </form>
            </div>
        </section>


<!-- Section des Services -->
        <section class="admin-page">
            <h2 class="section-title">Services</h2>
            <div class="sections-container" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Nom du Service</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <tr id="row-<?= $service['id']; ?>">
                                    <td><h3><?php echo htmlspecialchars($service['name']); ?></h3></td>
                                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                                    <td>
                                        <!-- Afficher l'image si elle existe -->
                                        <?php if (!empty($service['image'])): ?>
                                            <img src="<?= htmlspecialchars($service['image']); ?>" alt="Image du service" width="100">
                                        <?php else: ?>
                                            <p>Aucune image</p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="button update-button" data-service-id="<?= $service['id']; ?>" onclick="showEditForm(<?= $service['id']; ?>)">Modifier</button>
                                        <form action="index.php?page=services&action=delete" method="post" style="display:inline;" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                        <button type="submit" class="delete-button">Supprimer</button>
                                    </form>
                                    </td>
                                </tr>

                                <!-- Ligne pour le formulaire de modification, cachée par défaut -->
                        <tr id="edit-form-<?= $service['id']; ?>" class="edit-form" style="display:none;">
                            <td colspan="4">
                                <form action="index.php?page=services&action=update" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $service['id']; ?>">            
                                    <input type="text" id="edit_service_name_<?= $service['id']; ?>" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required>               
                                    <textarea id="edit_service_description_<?= $service['id']; ?>" name="description" required><?php echo htmlspecialchars($service['description']); ?></textarea>               
                                    <input type="file" id="edit_service_image_<?= $service['id']; ?>" name="image">

                                    <!-- Afficher l'image actuelle et permettre de la conserver -->
                                    <?php if (!empty($service['image'])): ?>
                                        <img src="<?= htmlspecialchars($service['image']); ?>" alt="Image du service">
                                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($service['image']); ?>">
                                    <?php endif; ?>

                                    <!-- Conteneur pour centrer les boutons -->
                                    <div class="button-container">
                                        <button type="submit" class="cta-button">Enregistrer les Modifications</button>
                                        <button type="button" class="button" onclick="hideEditForm(<?= $service['id']; ?>)">Annuler</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Aucun service trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <button id="toggle-create-form" class="cta-button">Ajouter un Service</button>

        <form action="index.php?page=services&action=create" method="POST" enctype="multipart/form-data" class="edit-form" id="create-service-form" style="display: none;">
            <label for="service_name">Nom du Service :</label>
            <input type="text" id="service_name" name="name" required>

            <label for="service_description">Description :</label>
            <textarea id="service_description" name="description" required></textarea>

            <label for="service_image">Image d'illustration :</label>
            <input type="file" id="service_image" name="image">

            <br>
            <div class="button-container">
                <button type="submit" class="cta-button">Ajouter le Service</button>
            </div>
        </form>

    </div>
</section>





<!-- Section des News -->
 <section>
        <div class="section">
            <h2 class="section-title">Actualités</h2>
            <div class="sections-container" style="display: none;">
                <table>
                    <tr>
                        <th>Titre</th>
                        <th>Contenu</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($news as $new): ?>
                    <tr>
                        <form method="POST" action="?page=admin&action=updateNews">
                            <td><input type="text" name="title" value="<?= htmlspecialchars($new['title']); ?>" required></td>
                            <td><textarea name="content" required><?= htmlspecialchars($new['content']); ?></textarea></td>
                            <input type="hidden" name="id" value="<?= $new['id']; ?>">
                            <td>
                                <button type="submit" class="button">Mettre à jour</button>
                                <form action="?page=admin&action=deleteNews&id=<?= $new['id']; ?>" method="post" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?');">
                                    <button type="submit" class="delete-button">Supprimer</button>
                                </form>
                            </td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <form action="?page=admin&action=createNews" method="POST">
                    <input type="text" name="title" placeholder="Titre" required>
                    <textarea name="content" placeholder="Contenu" required></textarea>
                    <button type="submit" class="cta-button">Ajouter Actualité</button>
                </form>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Horaires d'Ouverture</h2>
            <div class="sections-container" style="display: none;">
                <form action="index.php?page=admin&action=updateOpeningHours" method="POST">
                    <table>
                        <tr>
                            <th class="column-day">Jour</th>
                            <th class="column-open">Heure d'Ouverture</th>
                            <th class="column-close">Heure de Fermeture</th>
                        </tr>
                        <?php 
                        $daysOfWeek = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 0 => 'Dimanche'];
                        $orderedDays = [1, 2, 3, 4, 5, 6, 0];
                        foreach ($orderedDays as $day): 
                            $hour = isset($openingHours[$day]) ? $openingHours[$day] : ['start_time' => '', 'end_time' => ''];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($daysOfWeek[$day]); ?></td>
                            <td><input type="time" name="hours[<?php echo $day; ?>][start_time]" value="<?php echo htmlspecialchars($hour['start_time']); ?>"></td>
                            <td><input type="time" name="hours[<?php echo $day; ?>][end_time]" value="<?php echo htmlspecialchars($hour['end_time']); ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <button type="submit" class="cta-button">Mettre à jour les Horaires</button>
                </form>
            </div>
        </div>
</section>
    </main>
    
    <?php include 'footer.php'; ?>

    <script src="../assets/js/admin.js"></script>
</body>
</html>