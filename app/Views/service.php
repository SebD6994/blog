<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos services</title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- Chemin vers votre fichier CSS -->
</head>
<body>
    <header>
        <h1>Liste des Services</h1>
        <nav>
            <ul>
                <li><a href="index.php?page=home">Accueil</a></li>
                <li><a href="index.php?page=patients">Patients</a></li>
                <li><a href="index.php?page=appointments">Rendez-vous</a></li>
                <li><a href="index.php?page=services">Services</a></li>
                <li><a href="index.php?page=news">Actualités</a></li>
                <?php if (isset($_SESSION['patient'])): ?>
                    <li>
                        <a href="index.php?page=patients&action=logout">Se déconnecter</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
    <h1>Services Proposés par le Dr. Dupont</h1>

        <p>Le Dr. Dupont offre une large gamme de services dentaires pour répondre à vos besoins. Voici les services disponibles :</p>
        <ul>
            <?php foreach ($services as $service): ?>
                <li>
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <?php echo htmlspecialchars($service['description']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <p>Pour prendre rendez-vous pour l'un de ces services, veuillez vous rendre sur la page <a href="index.php?page=appointments">Rendez-vous</a>.</p>
    </main>

    <footer>
        <p>&copy; 2024 Cabinet du Dr. Dupont. Tous droits réservés.</p>
    </footer>
</body>
</html>