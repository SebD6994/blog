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
        header("Location: index.php?page=admin_patient");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace personnel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    
    <?php include 'header.php'; ?>

    <main>
    <?php if (isset($message)): ?>
        <div class="message">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['patient'])): ?>
        

        <div class="patient-info form-style">
            <h2>Mes Informations</h2>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($_SESSION['patient']['first_name'] ?? 'N/A'); ?></p>
            <p><strong>Nom :</strong> <?= htmlspecialchars($_SESSION['patient']['last_name'] ?? 'N/A'); ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($_SESSION['patient']['email'] ?? 'N/A'); ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($_SESSION['patient']['phone'] ?? 'N/A'); ?></p>
            <button id="editButton" class="button">Modifier</button>
        </div>

        <!-- Formulaire de modification caché par défaut -->
        <div id="editForm" class="form-style" style="display:none;">
            <form action="index.php?page=patients&action=update" method="POST">
                <h3>Modifier les informations</h3>
                
                <!-- Ajoutez un champ caché pour l'ID du patient -->
                <input type="hidden" name="id" value="<?= htmlspecialchars($_SESSION['patient']['id'] ?? ''); ?>">

                <label for="first_name">Prénom :</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($_SESSION['patient']['first_name'] ?? ''); ?>" required>

                <label for="last_name">Nom :</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($_SESSION['patient']['last_name'] ?? ''); ?>" required>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['patient']['email'] ?? ''); ?>" required>

                <label for="phone">Téléphone :</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($_SESSION['patient']['phone'] ?? ''); ?>" required>

                <button type="submit" class ="button">Mettre à jour</button>
            </form>
        </div>
 

        <h2>Mes Rendez-vous</h2>
            <section class="appointments-section">
                <?php if (!empty($appointments)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Service</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <?php 
                                    $dateTime = new DateTime($appointment['appointment_date']);
                                    $date = $dateTime->format('d/m/Y');
                                    $heure = $dateTime->format('H:i');
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($date); ?></td>
                                    <td><?= htmlspecialchars($heure); ?></td>
                                    <td><?= htmlspecialchars($appointment['service_name'] ?? 'Service non spécifié'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucun rendez-vous trouvé.</p>
                <?php endif; ?>
                <a href="index.php?page=appointments" class="cta-button">Modifier un rendez-vous</a>
            </section>
                
        <?php else: ?>         

                <!-- Bannière -->
                <?php if (isset($bannerImagePath)): ?>
                    <div class="banner">
                        <img src="<?php echo htmlspecialchars($bannerImagePath); ?>" alt="Bannière de la clinique" class="banner-image">
                    </div>
                <?php else: ?>
                    <p>Aucune bannière disponible.</p>
                <?php endif; ?>
                
            <div class="login-section form-style">
                <h2>Connexion</h2>
                <form action="index.php?page=patients&action=login" method="POST">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>

                    <button type="submit" class="cta-button">Se connecter</button>
                </form>
            </div>

            <div id="register-form" class="form-style" style="display: block;">
                <h2>Inscription</h2>
                <form action="index.php?page=patients&action=create" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Prénom</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nom</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <input type="text" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <input type="hidden" name="role" value="user">
                    <button type="submit" class="cta-button">S'inscrire</button>
                </form>
            </div>

            <?php if (isset($_SESSION['successMessage'])): ?>
                <div class="success-message">
                    <?= htmlspecialchars($_SESSION['successMessage']); ?>
                    <?php unset($_SESSION['successMessage']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['errorMessage'])): ?>
                <div class="error-message">
                    <?= htmlspecialchars($_SESSION['errorMessage']); ?>
                    <?php unset($_SESSION['errorMessage']); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>

    <script src="../assets/js/patient.js"></script>
</body>
</html>