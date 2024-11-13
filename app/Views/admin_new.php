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
if (!isset($_SESSION['patient']['role']) || $_SESSION['patient']['role'] !== 'admin') {
    header("Location: index.php?page=patients");
    exit();
}
?>


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
        <section class="admin-page">
            <h2>Actualités</h2>
            <div class="sections-container">
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($news)): ?>
                            <?php foreach ($news as $new): ?>
                                <tr id="row-<?= $new['id']; ?>">
                                    <td><h3><?php echo htmlspecialchars($new['title']); ?></h3></td>
                                    <td>
                                        <button type="button" class="button" onclick="showEditForm(<?= $new['id']; ?>)">Modifier</button>
                                        <form action="?page=admin_news&action=delete&id=<?= $new['id']; ?>" method="POST" style="display:inline;" onsubmit="return confirmDelete('Êtes-vous sûr de vouloir supprimer cette actualité ?');">
                                            <input type="hidden" name="id" value="<?= $new['id']; ?>">
                                            <button type="submit" class="delete-button">Supprimer</button>
                                        </form>
                                    </td>

                                </tr>

                                <!-- Formulaire de mise à jour (caché par défaut) -->
                                <tr id="update-form-news-<?= $new['id']; ?>" class="edit-form" style="display: none;">
                                    <td colspan="2">
                                        <form method="POST" action="?page=admin_news&action=update" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $new['id']; ?>">
                                            <input type="text" id="edit_news_title_<?= $new['id']; ?>" name="title" value="<?= htmlspecialchars($new['title']); ?>" required style="width: 100%;">

                                            <textarea id="edit_news_content_<?= $new['id']; ?>" name="content" required style="width: 100%;"><?php echo htmlspecialchars($new['content']); ?></textarea>

                                            <!-- Display existing image -->
                                            <div>
                                                <?php if (!empty($new['image_path'])): // Check the correct field name ?>
                                                    <img src="<?= htmlspecialchars($new['image_path']); ?>" alt="Image de la news">
                                                    <input type="hidden" name="existing_image" value="<?= htmlspecialchars($new['image_path']); ?>">
                                                <?php endif; ?>
                                            </div>

                                            <!-- Input for new image upload -->
                                            <label for="edit_service_image_<?= $new['id']; ?>">Nouvelle image (optionnel) :</label>
                                            <input type="file" id="edit_service_image_<?= $new['id']; ?>" name="image" accept="image/*">

                                            <div class="button-container">
                                                <button type="submit" class="button">Mettre à jour</button>
                                                <button type="button" class="button" onclick="hideEditForm(<?= $new['id']; ?>)">Annuler</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">Aucune actualité trouvée.</td>
                            </tr>
                        <?php endif; ?>

                        <!-- Formulaire de création -->
                        <tr>
                            <td colspan="2">
                                <form action="?page=admin_news&action=create" method="POST" class="edit-form" id="create-news-form" enctype="multipart/form-data">
                                    <input type="text" id="news_title" name="title" required style="width: 100%;">
                                    <textarea id="news_content" name="content" style="width: 100%;"></textarea>
                                    <input type="file" id="service_image" name="image" accept="image/*">
                                    <div class="button-container">
                                        <button type="submit" class="cta-button">Publier</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.tiny.cloud/1/8pmzn31l5fg9pakc6iubiw3hvsowgx2qq1116dbvyo8sfn8b/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#news_content',
      });
    </script>
    <script src="../assets/js/admin_news.js"></script>
</body>

</html>
