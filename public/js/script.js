import { fetchApi } from "./api.js";
import { chargerProduits } from "./products.js";

/**
 * Centralise la récupération des éléments DOM utilisés par la page.
 */
function getDom() {
    return {
        form: document.getElementById('form'),
        deleteAllBtn: document.getElementById('deleteAllBtn'),
        logoutBtn: document.getElementById('logoutBtn'),
        nomInput: document.getElementById('nom'),
        quantiteInput: document.getElementById('quantite')
    };
}

/**
 * Gère l'ajout d'un produit:
 * - bloque le submit HTML classique
 * - valide les champs
 * - envoie la requête API
 * - reset le formulaire
 * - recharge la liste affichée
 */
async function handleAddProduct(event, dom) {
    event.preventDefault();

    const nom = dom.nomInput?.value?.trim();
    const quantite = dom.quantiteInput?.value?.trim();
    if (!nom || !quantite) return;

    await fetchApi('api/addProduct.php', {
        method: 'POST',
        body: new URLSearchParams({ nom, quantite })
    });

    dom.form.reset();
    await chargerProduits();
}

/**
 * Supprime tous les produits après confirmation utilisateur.
 */
async function handleDeleteAll() {
    if (!confirm("Voulez-vous vraiment supprimer toutes les tâches ?")) return;

    await fetchApi('api/deleteAll.php', { method: 'POST' });
    await chargerProduits();
}

/**
 * Déconnecte l'utilisateur puis redirige vers login.
 */
async function handleLogout() {
    try {
        await fetchApi('api/logout.php', { method: 'POST' });
    } finally {
        window.location.href = 'login.html';
    }
}

/**
 * Ouvre/ferme le menu user au clic sur l'icône.
 */
function initUserMenu() {
    const userBtn = document.getElementById('user');
    const menu = document.querySelector('.connexionUser');
    const closeBtn = document.getElementById('closeUserMenu');

    if (!userBtn || !menu) return;

    userBtn.addEventListener('click', (e) => {
        e.preventDefault();
        menu.classList.toggle('select');
    });

    closeBtn?.addEventListener('click', () => {
        menu.classList.remove('select');
    });
}

/**
 * Point d'entrée de la page:
 * - récupère le DOM
 * - vérifie les éléments requis
 * - branche les listeners
 * - charge les produits au démarrage
 */
function main() {

    initUserMenu();

    const dom = getDom();
    if (!dom.form || !dom.deleteAllBtn || !dom.logoutBtn) return;

    dom.form.addEventListener('submit', (e) => void handleAddProduct(e, dom));
    dom.deleteAllBtn.addEventListener('click', () => void handleDeleteAll());
    dom.logoutBtn.addEventListener('click', () => void handleLogout());

    void chargerProduits();
}

// Lance l'initialisation
main();