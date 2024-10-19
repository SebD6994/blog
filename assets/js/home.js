let currentIndex = 0;

// Fonction pour afficher la diapositive actuelle
function showSlide(index) {
    const slides = document.querySelectorAll('.carousel-item');
    const totalSlides = slides.length;

    // S'assurer que l'index reste dans les limites
    currentIndex = (index + totalSlides) % totalSlides; // Gère les limites et boucle

    // Afficher l'image actuelle et masquer les autres
    slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === currentIndex); // Ajoute ou retire la classe active
    });

    // Mettre à jour les indicateurs
    const indicators = document.querySelectorAll('.indicator');
    indicators.forEach((indicator, i) => {
        indicator.classList.toggle('active', i === currentIndex); // Ajoute ou retire la classe active
    });
}

// Fonction pour déplacer d'une diapositive
function moveSlide(step) {
    showSlide(currentIndex + step);
}

// Fonction pour afficher la diapositive sélectionnée
function currentSlide(index) {
    showSlide(index);
}

// Afficher la première image au chargement
showSlide(currentIndex);