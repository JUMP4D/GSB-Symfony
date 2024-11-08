// public/js/filter.js

// Ajoute un écouteur d'événement pour le changement de sélection du mois
document.getElementById('monthSelect').addEventListener('change', function() {
    var selectedMonth = this.value;
    var rows = document.querySelectorAll('#ficheFraisTable tr');

    // Parcourt chaque ligne du tableau pour vérifier le mois
    rows.forEach(function(row) {
        var monthCell = row.cells[4];
        if (monthCell) {
            var month = monthCell.textContent.trim().split('-')[1];
            // Affiche ou masque la ligne en fonction du mois sélectionné
            if (selectedMonth === "" || month === selectedMonth) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});