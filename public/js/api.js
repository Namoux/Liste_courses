/**
 * Wrapper central pour les appels API.
 * - Envoie automatiquement les cookies de session (PHP)
 * - Gère le cas non authentifié (401)
 * - Lève une erreur pour toute réponse HTTP non OK
 */
export async function fetchApi(url, options = {}) {
    // Exécute la requête HTTP avec les options passées
    // credentials: 'include' est indispensable pour envoyer le cookie de session
    const res = await fetch(url, {
        credentials: 'include',
        ...options
    });

    // Si la session n'est plus valide, redirige vers la page de connexion
    if (res.status === 401) {
        window.location.href = 'login.html';
        throw new Error('Utilisateur non connecté');
    }

    // Pour tout autre statut d'erreur HTTP, on lève une exception
    if (!res.ok) {
        throw new Error(`Erreur API (${res.status})`);
    }

    // Retourne la réponse brute pour laisser le code appelant faire res.json(), etc.
    return res;
}