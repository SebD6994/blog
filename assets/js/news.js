function toggleArticle(button) {
    // Trouver le parent <li> de l'article
    const articleItem = button.closest('li.news-item');

    // Trouver tous les articles complets
    const allArticles = document.querySelectorAll('.full-article');

    // Boucle pour fermer tous les articles ouverts
    allArticles.forEach(articleContent => {
        // Si cet article n'est pas celui qui a été cliqué
        if (articleContent !== articleItem.nextElementSibling) {
            articleContent.style.display = "none"; // Cache le contenu
            // Récupérer le bouton correspondant et réinitialiser le texte
            const correspondingButton = articleContent.previousElementSibling.querySelector('.cta-button');
            if (correspondingButton) {
                correspondingButton.textContent = "Lire la suite"; // Réinitialise le texte du bouton
            }
        }
    });

    // Trouver le contenu complet juste après cet article
    const articleContent = articleItem.nextElementSibling; // Sélectionne le contenu de l'article suivant

    if (articleContent && articleContent.classList.contains('full-article')) {
        // Gérer l'affichage de cet article
        if (articleContent.style.display === "none" || articleContent.style.display === "") {
            articleContent.style.display = "block"; // Affiche le contenu
            button.textContent = "Masquer l'article"; // Change le texte du bouton
            
            // Faire défiler vers le contenu de l'article
            articleContent.scrollIntoView({ behavior: 'smooth', block: 'start' }); // Défilement en douceur
        } else {
            articleContent.style.display = "none"; // Cache le contenu
            button.textContent = "Lire la suite"; // Réinitialise le texte du bouton
        }
    }
}