// Stockage temporaire des catégories et de leur unité associée
const categories = [];

document.addEventListener("DOMContentLoaded", () => {
  const formCategorie = document.getElementById("formCategorie");
  const inputCategorie = document.getElementById("categorieInput");
  const inputUnite = document.getElementById("uniteInput");
  const liste = document.getElementById("categorieList");

  // Soumission du formulaire
  formCategorie.addEventListener("submit", (e) => {
    e.preventDefault();

    const nomCategorie = inputCategorie.value.trim();
    const unite = inputUnite.value.trim();

    if (nomCategorie && !categories.includes(nomCategorie)) {
      categories.push(nomCategorie);

      // Créer le texte affiché : avec ou sans unité
      const affichage = unite
        ? `${nomCategorie} → ${unite}`
        : `${nomCategorie} → (aucune unité)`;

      const li = document.createElement("li");
      li.textContent = affichage;
      liste.appendChild(li);

      afficherMessage("Catégorie ajoutée.");
      inputCategorie.value = "";
      inputUnite.value = "";
    }
  });
});

// Affiche un message de confirmation temporaire
function afficherMessage(texte) {
  const message = document.getElementById("confirmation-message");
  message.textContent = `✅ ${texte}`;
  message.style.display = "block";
  setTimeout(() => {
    message.style.display = "none";
  }, 4000);
}
