const form = document.getElementById('loginForm');
const message = document.getElementById('message');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    try {
        const res = await fetch('api/login.php', {
            method: 'POST',
            credentials: 'include', // permet à PHP de gérer la session
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ username, password })
        });

        const raw = await res.text();
        let data = {};

        try {
            data = JSON.parse(raw);
        } catch {
            data = { error: 'Réponse serveur invalide' };
        }

        if (res.ok) {
            const checkRes = await fetch('api/getProduct.php', {
                method: 'GET',
                credentials: 'include',
                cache: 'no-store'
            });

            if (checkRes.status === 401) {
                message.textContent = 'Connexion OK mais session non conservée. Vérifie les cookies du navigateur.';
                return;
            }

            window.location.replace('index.html');
        } else {
            message.textContent = data.error || 'Erreur de connexion';
        }
    } catch (err) {
        message.textContent = 'Erreur réseau';
        console.error(err);
    }
});
