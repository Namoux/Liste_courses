// Récupère les éléments du DOM
const form = document.getElementById('loginForm');
const message = document.getElementById('message');

// Sécurité minimale: si le HTML n'est pas conforme, on stoppe proprement
if (!form || !message) {
    console.error('Éléments loginForm/message introuvables.');
} else {
    // Écoute la soumission du formulaire de connexion
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Lit les champs utilisateur
        const username = document.getElementById('username')?.value?.trim() ?? '';
        const password = document.getElementById('password')?.value ?? '';

        // Validation simple côté front
        if (!username || !password) {
            message.textContent = 'Veuillez remplir tous les champs.';
            return;
        }

        // Réinitialise le message avant l'appel API
        message.textContent = '';

        try {
            // Appel API login (session PHP via cookies)
            const res = await fetch('api/login.php', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ username, password })
            });

            // Tente de lire la réponse JSON (sans casser si JSON invalide)
            let data = {};
            try {
                data = await res.json();
            } catch {
                data = { error: 'Réponse serveur invalide' };
            }

            // Si succès, redirection vers la page principale
            if (res.ok) {
                window.location.replace('index.html');
                return;
            }

            // Gestion des erreurs métier HTTP
            if (res.status === 400) {
                message.textContent = 'Champs manquants.';
            } else if (res.status === 401) {
                message.textContent = 'Identifiants invalides.';
            } else {
                message.textContent = data.error || 'Erreur de connexion.';
            }
        } catch (err) {
            // Erreur réseau (serveur down, coupure, CORS, etc.)
            message.textContent = 'Erreur réseau.';
            console.error(err);
        }
    });
}