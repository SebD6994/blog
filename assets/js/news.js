function toggleArticle(button) {
    const articleItem = button.closest('li.news-item');
    const allArticles = document.querySelectorAll('.full-article');

    allArticles.forEach(articleContent => {
        if (articleContent !== articleItem.nextElementSibling) {
            articleContent.style.display = "none";
            const correspondingButton = articleContent.previousElementSibling.querySelector('.cta-button');
            if (correspondingButton) {
                correspondingButton.textContent = "Lire la suite";
            }
        }
    });

    const articleContent = articleItem.nextElementSibling;

    if (articleContent && articleContent.classList.contains('full-article')) {
        if (articleContent.style.display === "none" || articleContent.style.display === "") {
            articleContent.style.display = "block";
            button.textContent = "Masquer l'article";
            articleContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            articleContent.style.display = "none";
            button.textContent = "Lire la suite";
        }
    }
}