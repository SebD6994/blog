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
    <title>Gestion des Actualités</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Chemin vers votre fichier CSS -->
</head>
<body>
    
    <?php include 'header.php'; ?>

    <main>
        <h2>Liste des Actualités</h2>
        <ul>
            <?php foreach ($newsItems as $news): ?>
                <li>
                    <h2><?php echo htmlspecialchars($news['title']); ?></h2> 
                    <?php echo htmlspecialchars($news['content']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>

    <?php include 'footer.php'; ?>
    
</body>
</html>