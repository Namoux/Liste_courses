// Récupère les éléments
const liste = document.getElementById('liste');
const form = document.getElementById('form');
const deleteAllBtn = document.getElementById('deleteAllBtn');
const logoutBtn = document.getElementById('logoutBtn');

function fetchApi(url, options = {}) {
    return fetch(url, {
        credentials: 'include',
        ...options
    }).then(res => {
        if (res.status === 401) {
            window.location.href = 'login.html';
            throw new Error('Utilisateur non connecté');
        }

        if (!res.ok) {
            throw new Error(`Erreur API (${res.status})`);
        }

        return res;
    });
}

// Charger les produits depuis l'API
function chargerProduits() {
    fetchApi('api/getProduct.php')
        .then(res => res.json())
        .then(data => {
            if (!Array.isArray(data)) {
                return;
            }

            liste.innerHTML = '';

            // Trier : produits non cochés d'abord
            data.sort((a, b) => a.checked - b.checked);

            data.forEach(product => {
                const li = document.createElement('li');
                li.id = 'item-' + product.id;

                // checkbox
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = product.checked == 1;
                checkbox.addEventListener('change', () => toggleProduct(product.id, checkbox.checked));

                // texte du produit
                const text = document.createElement('span');
                text.textContent = `${product.nom} (${product.quantite})`;
                if (checkbox.checked) {
                    text.style.opacity = '0.5';
                }

                // bouton supprimer
                const btnDelete = document.createElement('button');
                btnDelete.textContent = '❌';
                btnDelete.addEventListener('click', () => supprimer(product.id));

                li.appendChild(checkbox);
                li.appendChild(text);
                li.appendChild(btnDelete);

                // si coché, on le met à la fin
                if (checkbox.checked) {
                    liste.appendChild(li);
                } else {
                    liste.prepend(li);
                }
            });
        })
        .catch(err => {
            if (err.message !== 'Utilisateur non connecté') {
                console.error(err);
            }
        });
}

// Ajouter un produit
form.addEventListener('submit', event => {
    event.preventDefault();
    const nom = document.getElementById('nom').value;
    const quantite = document.getElementById('quantite').value;

    fetchApi('api/addProduct.php', {
        method: 'POST',
        body: new URLSearchParams({ nom, quantite })
    }).then(() => {
        form.reset();
        chargerProduits();
    });
});

// Supprimer un produit
function supprimer(id) {
    fetchApi('api/deleteProduct.php', {
        method: 'POST',
        body: new URLSearchParams({ id })
    }).then(() => {
        document.getElementById('item-' + id)?.remove();
    });
}

// Supprimer tous les produits
deleteAllBtn.addEventListener('click', () => {
    if (!confirm("Voulez-vous vraiment supprimer toutes les tâches ?")) return;

    fetchApi('api/deleteAll.php', {
        method: 'POST',
    })
        .then(() => chargerProduits());
});

// Marquer un produit comme fait / non fait
function toggleProduct(id, checked) {
    fetchApi('api/toggleProduct.php', {
        method: 'POST',
        body: new URLSearchParams({ id, checked })
    }).then(() => chargerProduits());
}

logoutBtn.addEventListener('click', () => {
    fetch('api/logout.php', {
        method: 'POST',
        credentials: 'include'
    }).finally(() => {
        window.location.href = 'login.html';
    });
});

// Initialisation
chargerProduits();
