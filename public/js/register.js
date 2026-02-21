// Récupère les éléments du DOM
const form = document.getElementById('registerForm');
const message = document.getElementById('message');

// Sécurité minimale: si le HTML n'est pas conforme, on évite un crash JS
if (!form || !message) {
    console.error('Éléments registerForm/message introuvables.');
} else {
    // Écoute la soumission du formulaire d'inscription
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Lit et nettoie les champs
        const username = document.getElementById('username')?.value?.trim() ?? '';
        const password = document.getElementById('password')?.value ?? '';

        // Validation simple côté front
        if (!username || !password) {
            message.textContent = 'Veuillez remplir tous les champs.';
            return;
        }

        // Nettoie le message avant l'appel API
        message.textContent = '';

        try {
            // Appel API register (session/cookies autorisés)
            const res = await fetch('api/register.php', {
                method: 'POST',
                credentials: 'include',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ username, password })
            });

            // Lecture JSON tolérante
            let data = {};
            try {
                data = await res.json();
            } catch {
                data = { error: 'Réponse serveur invalide' };
            }

            // Succès: redirection vers login
            if (res.ok) {
                window.location.replace('login.html');
                return;
            }

            // Gestion explicite des erreurs backend
            if (res.status === 400) {
                message.textContent = 'Champs manquants.';
            } else if (res.status === 409) {
                message.textContent = 'Nom d’utilisateur déjà pris.';
            } else {
                message.textContent = data.error || 'Erreur lors de l’inscription.';
            }
        } catch (err) {
            // Erreur réseau (API indisponible, CORS, etc.)
            message.textContent = 'Erreur réseau.';
            console.error(err);
        }
    });
}