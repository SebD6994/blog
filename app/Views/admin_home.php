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
    <title>Nos actualités</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <?php include 'header.php'; ?>
    <main>

    <h2 class="section-title">Gestion de la Bannière</h2>
    <!-- Afficher l'aperçu de la bannière actuelle -->
    <?php if ($bannerImagePath): ?>
        <div class="banner">
            <h3>Aperçu de la bannière actuelle :</h3>
            <img src="<?php echo htmlspecialchars($bannerImagePath); ?>" alt="Aperçu de la bannière" class="banner-image">
        </div>
    <?php else: ?>
        <p>Aucune bannière disponible.</p>
    <?php endif; ?>


    <!-- Section pour modifier l'image de la bannière -->
    <div class="edit-form">
    <form action="index.php?page=admin_home&action=updateBannerImage" method="POST" enctype="multipart/form-data">
        <!-- ID caché pour identifier quel enregistrement de la table 'settings' sera mis à jour -->
        <input type="hidden" name="setting_id" value="<?php echo isset($settingId) ? htmlspecialchars($settingId) : ''; ?>">

        <label for="banner-image">Sélectionnez une nouvelle image pour la bannière :</label>
        <input type="file" name="new_banner" id="banner-image" accept="image/*" required>

        <!-- Autre contenu ou description si nécessaire -->
        <label for="description">Description (facultative) :</label>
        <textarea name="value" id="description"><?php echo isset($bannerDescription) ? htmlspecialchars($bannerDescription) : ''; ?></textarea> <!-- Ajout de isset() -->

        <button type="submit" class="cta-button">Mettre à jour la bannière</button>
    </form>
</div>




    <!-- Section des Horaires -->
    <div class="section">
        <h2 class="section-title">Horaires d'Ouverture</h2>
        <div class="sections-container">
            <form action="index.php?page=admin_home&action=updateOpeningHours" method="POST">
                <table class="opening-hours-table">
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

    <div class="section">
    <h2>Images actuelles de la clinique</h2>
    <?php if (!empty($clinicImages)): ?>
        <table class="clinic-image-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clinicImages as $index => $image): ?>
                    <tr id="row-<?= $image['id']; ?>" class="<?= $index % 2 === 0 ? 'service-even' : 'service-odd'; ?>">
                        <td>
                            <input type="text" name="description" value="<?php echo htmlspecialchars($image['description']); ?>" readonly class="form-input">
                        </td>
                        <td>
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Image de la clinique" width="100" class="existing-image">
                        </td>
                        <td>
                            <div class="image-controls">
                                <!-- Formulaire pour modifier l'image -->
                                <form action="index.php?page=admin_home&action=updateClinicImage" method="POST" enctype="multipart/form-data" class="update-form">
                                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                    <label for="new_image_<?php echo $image['id']; ?>">Nouvelle image :</label>
                                    <input type="file" name="new_image" id="new_image_<?php echo $image['id']; ?>" accept="image/*" required class="form-input">
                                    <button type="submit" class="button">Modifier</button>
                                </form>

                                <!-- Formulaire pour supprimer l'image -->
                                <form action="index.php?page=admin_home&action=deleteClinicImage" method="POST" class="delete-form" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                    <button type="submit" class="delete-button">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune image pour le moment.</p>
    <?php endif; ?>
</div>

    <!-- Formulaire pour ajouter une image -->
    <div class="section">
        <h3>Ajouter une nouvelle image</h3>
        <form action="index.php?page=admin_home&action=addClinicImage" method="POST" enctype="multipart/form-data" class="edit-form">
            <label for="clinic_image">Sélectionnez une image :</label>
            <input type="file" name="clinic_image" id="clinic_image" required class="form-input">
            
            <label for="description">Description :</label>
            <input type="text" name="description" id="description" required class="form-input">
            
            <button type="submit" class="cta-button">Ajouter l'image</button>
        </form>
    </div>
</main>




<?php include 'footer.php'; ?>

<script src="../assets/js/admin_home.js"></script>
</body>
</html>
