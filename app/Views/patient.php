<?php 
// Démarrer la session si elle n'a pas encore été démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
        <?php if (isset($_SESSION['patient'])): ?>
            <h2>Informations du Patient Connecté</h2>
            <p><strong>Patient ID :</strong> <?= htmlspecialchars($_SESSION['patient']['id'] ?? 'N/A'); ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($_SESSION['patient']['first_name'] ?? 'N/A'); ?></p>
            <p><strong>Nom :</strong> <?= htmlspecialchars($_SESSION['patient']['last_name'] ?? 'N/A'); ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($_SESSION['patient']['email'] ?? 'N/A'); ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($_SESSION['patient']['phone'] ?? 'N/A'); ?></p>

            <h2>Mes Rendez-vous</h2>
            <ul>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <li>
                            <strong>Rendez-vous ID :</strong> <?= htmlspecialchars($appointment['id']); ?> - 
                            <strong>Date :</strong> <?= htmlspecialchars($appointment['appointment_date']); ?> - 
                            <strong>Heure :</strong> <?= htmlspecialchars($appointment['appointment_time']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Aucun rendez-vous trouvé.</li>
                <?php endif; ?>
            </ul>

            <div>
                <a href="index.php?page=appointments" class="button">Prendre rendez-vous</a>
            </div>

        <?php else: ?>
            <p>Vous devez être connecté pour voir vos informations et rendez-vous.</p>

            <section>
                <h2>Connexion Patient</h2>
                <form action="index.php?page=patients&action=login" method="POST">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required> <!-- Mise à jour du champ 'email' -->
                    
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                    
                    <button type="submit">Se connecter</button>
                </form>
            </section>

            <section>
                <h2>Créer un compte patient</h2>
                <form action="index.php?page=patients&action=create" method="POST">
                    <label for="first_name">Prénom :</label>
                    <input type="text" id="first_name" name="first_name" required>
        
                    <label for="last_name">Nom :</label>
                    <input type="text" id="last_name" name="last_name" required>
        
                    <label for="email_register">Email :</label>
                    <input type="email" id="email_register" name="email" required>
        
                    <label for="phone">Téléphone :</label>
                    <input type="text" id="phone" name="phone" required>
        
                    <label for="password_register">Mot de passe :</label>
                    <input type="password" id="password_register" name="password" required>
        
                    <label for="confirm_password">Confirmer le mot de passe :</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
        
                    <input type="hidden" name="role" value="patient"> <!-- Défini le rôle comme 'patient' -->

                    <button type="submit">Créer un compte</button>
                </form>
            </section>
        <?php endif; ?>

        <!-- Affichage des messages d'erreur et de succès -->
        <?php if (!empty($errorMessage)): ?>
            <div class="error"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="success"><?= htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>