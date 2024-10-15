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
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Chemin vers votre fichier CSS -->
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
    <h2>Services Proposés par le Dr. Dupont</h2>

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