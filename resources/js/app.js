import './bootstrap';

// Ajouter une classe CSS lorsque l'élément est survolé
document.querySelectorAll('.input-animate').forEach(element => {
    element.addEventListener('mouseenter', function() {
        this.classList.add('zoom');
    });

    // Supprimer la classe CSS lorsque l'utilisateur quitte l'élément
    element.addEventListener('mouseleave', function() {
        this.classList.remove('zoom');
    });
});
