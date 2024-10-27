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
        header("Location: index.php?page=admin_news");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Chemin vers votre fichier CSS -->
</head>
<body>
    
    <?php include 'header.php'; ?>

    <main>
        <h2>Liste des Actualités</h2>
        <ul>
        <?php foreach ($newsItems as $index => $news): ?>
            <li class="news-item <?php echo $index % 2 === 0 ? 'even' : 'odd'; ?>">
                <div class="image-container">
                    <?php 
                    // Vérifiez si l'image existe et affichez-la
                    if (isset($news['image_path']) && !empty($news['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($news['image_path']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>">
                    <?php else: ?>
                        <p>Pas d'image disponible</p>
                    <?php endif; ?>
                </div>
                
                <div class="news-content">
                    <h2><?php echo htmlspecialchars($news['title']); ?></h2>
                    <!-- Bouton "Lire la suite" -->
                    <button class="cta-button" onclick="toggleArticle(this)">Lire la suite</button>
                </div>
            </li>

            <!-- Contenu complet qui apparaîtra sous le conteneur existant -->
            <div class="full-article" style="display: none;">
                <?php echo $news['content']; // Afficher le contenu sans htmlspecialchars pour garder le HTML ?>
            </div>
        <?php endforeach; ?>
        </ul>
    </main>

    <?php include 'footer.php'; ?>

    <!-- Inclure le fichier JavaScript externe -->
    <script src="../assets/js/news.js"></script> <!-- Chemin vers votre fichier JS -->
    
</body>
</html>
