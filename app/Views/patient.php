<?php 
// Démarrer la session si elle n'a pas encore été démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Exemple de messages de session (à définir lors des actions dans vos scripts)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Supprimer le message après l'avoir affiché
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte et Connexion - Dr. Dupont</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- Chemin vers votre fichier CSS -->
</head>
<body>
    <header>
        <h1>Bienvenue chez Dr. Dupont</h1>
        <nav>
            <ul>
                <li><a href="index.php?page=home">Accueil</a></li>
                <li><a href="index.php?page=patients">Patients</a></li>
                <li><a href="index.php?page=appointments">Rendez-vous</a></li>
                <li><a href="index.php?page=services">Services</a></li>
                <li><a href="index.php?page=news">Actualités</a></li>
                <?php if (isset($_SESSION['patient'])): ?>
                    <li><a href="index.php?page=patients&action=logout">Se déconnecter</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (isset($message)): ?>
            <div class="message">
                <?= htmlspecialchars($message); ?> <!-- Affichage du message -->
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['patient'])): ?>
            <h2>Informations du Patient Connecté</h2>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($_SESSION['patient']['first_name'] ?? 'N/A'); ?></p>
            <p><strong>Nom :</strong> <?= htmlspecialchars($_SESSION['patient']['last_name'] ?? 'N/A'); ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($_SESSION['patient']['email'] ?? 'N/A'); ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($_SESSION['patient']['phone'] ?? 'N/A'); ?></p>

            <h2>Modifier mes Informations</h2>
            <form action="index.php?page=patients&action=update" method="POST">
                <label for="first_name">Prénom :</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($_SESSION['patient']['first_name'] ?? ''); ?>" required>
        
                <label for="last_name">Nom :</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($_SESSION['patient']['last_name'] ?? ''); ?>" required>
        
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['patient']['email'] ?? ''); ?>" required>
        
                <label for="phone">Téléphone :</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($_SESSION['patient']['phone'] ?? ''); ?>" required>
        
                <button type="submit">Mettre à jour mes informations</button>
            </form>

            <h2>Mes Rendez-vous</h2>
            <ul>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <?php 
                            // Extraction de la date et de l'heure
                            $dateTime = new DateTime($appointment['appointment_date']); // Crée un objet DateTime
                            $date = $dateTime->format('d/m/Y'); // Formate la date
                            $heure = $dateTime->format('H:i'); // Formate l'heure
                        ?>
                        <li>
                            <strong>Date :</strong> <?= htmlspecialchars($date); ?> - 
                            <strong>Heure :</strong> <?= htmlspecialchars($heure); ?> - 
                            <strong>Service :</strong> <?= htmlspecialchars($appointment['service_name'] ?? 'Service non spécifié'); ?>

                            <!-- Bouton pour rediriger vers la page appointments -->
                            <a href="index.php?page=appointments" class="button">Modifier ce rendez-vous</a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Aucun rendez-vous trouvé.</li>
                <?php endif; ?>
            </ul>

        <?php else: ?>
            <h2>Connexion</h2>
            <form action="index.php?page=patients&action=login" method="POST">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
        
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
        
                <button type="submit">Se connecter</button>
            </form>

            <h2>Inscription</h2>
            <form action="index.php?page=patients&action=create" method="POST">
                <label for="first_name">Prénom :</label>
                <input type="text" id="first_name" name="first_name" required>
        
                <label for="last_name">Nom :</label>
                <input type="text" id="last_name" name="last_name" required>
        
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
        
                <label for="phone">Téléphone :</label>
                <input type="text" id="phone" name="phone" required>
        
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
        
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <!-- Champ caché pour le rôle -->
                <input type="hidden" name="role" value="user">
        
                <?php if (isset($registrationError)): ?>
                    <p style="color: red;"><?= htmlspecialchars($registrationError); ?></p> <!-- Affichage d'erreur d'inscription -->
                <?php endif; ?>
        
                <button type="submit">S'inscrire</button>
            </form>

            <?php if (isset($_SESSION['successMessage'])): ?>
                <div class="success-message">
                    <?= htmlspecialchars($_SESSION['successMessage']); ?>
                    <?php unset($_SESSION['successMessage']); // Supprimer le message après l'affichage ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['errorMessage'])): ?>
                <div class="error-message">
                    <?= htmlspecialchars($_SESSION['errorMessage']); ?>
                    <?php unset($_SESSION['errorMessage']); // Supprimer le message après l'affichage ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>