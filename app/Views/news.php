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
                    <?php if (!empty($news['image'])): ?>
                        <img src="<?php echo htmlspecialchars($news['image']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" style="max-width: 100%; height: auto;">
                    <?php else: ?>
                        <p>Pas d'image disponible</p>
                    <?php endif; ?>
                    <p><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>

    <?php include 'footer.php'; ?>
    
</body>
</html>
