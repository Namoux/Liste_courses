const form = document.getElementById('registerForm');
const message = document.getElementById('message');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    try {
        const res = await fetch('api/register.php', {
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
            window.location.href = 'login.html';
        } else {
            message.textContent = data.error || 'Erreur lors de l’inscription';
        }
    } catch (err) {
        message.textContent = 'Erreur réseau';
        console.error(err);
    }
});
