import { fetchApi } from "./api.js";

/**
 * Charge la liste des produits depuis l'API et les affiche dans le DOM.
 */
export async function chargerProduits() {
    try {
        // Appel API pour récupérer tous les produits de l'utilisateur connecté
        const res = await fetchApi('api/getProduct.php');
        const data = await res.json();

        // Sécurité: on attend un tableau
        if (!Array.isArray(data)) return;

        // Conteneur UL/OL dans lequel afficher les éléments
        const liste = document.getElementById('liste');
        if (!liste) return;

        // Nettoie la liste avant de réafficher
        liste.innerHTML = '';

        // Tri: produits non cochés d'abord
        data.sort((a, b) => a.checked - b.checked);

        data.forEach(product => {
            // Élément de ligne
            const li = document.createElement('li');
            li.id = `item-${product.id}`;

            // Checkbox "fait / non fait"
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = product.checked == 1;
            checkbox.addEventListener('change', () => void toggleProduct(product.id, checkbox.checked));

            // Texte du produit
            const text = document.createElement('span');
            text.textContent = `${product.nom} (${product.quantite})`;

            // Style visuel si coché
            if (checkbox.checked) text.style.opacity = '0.5';

            // Bouton suppression
            const btnDelete = document.createElement('button');
            btnDelete.textContent = '❌';
            btnDelete.addEventListener('click', () => void supprimer(product.id));

            // Assemble les éléments dans la ligne
            li.append(checkbox, text, btnDelete);

            // Place les cochés en bas, non cochés en haut
            checkbox.checked ? liste.appendChild(li) : liste.prepend(li);
        });
    } catch (err) {
        // Ignore le cas déjà géré par fetchApi (redirection 401)
        if (err.message !== 'Utilisateur non connecté') {
            console.error(err);
        }
    }
}

/**
 * Supprime un produit côté API puis dans le DOM.
 */
export async function supprimer(id) {
    await fetchApi('api/deleteProduct.php', {
        method: 'POST',
        body: new URLSearchParams({ id })
    });

    // Retire l'élément de la page sans recharger toute la liste
    document.getElementById(`item-${id}`)?.remove();
}

/**
 * Met à jour l'état "checked" d'un produit puis recharge la liste.
 */
export async function toggleProduct(id, checked) {
    await fetchApi('api/toggleProduct.php', {
        method: 'POST',
        body: new URLSearchParams({ id, checked })
    });

    // Recharge pour conserver l'ordre (non cochés puis cochés)
    await chargerProduits();
}