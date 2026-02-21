<?php

declare(strict_types=1);

class ProductRepository
{
    /**
     * Injection de la connexion PDO.
     */
    public function __construct(private PDO $pdo) {}

    /**
     * Récupère tous les produits d'un utilisateur.
     * Tri: non cochés d'abord, puis plus récents.
     */
    public function findAllByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, quantite, checked
             FROM products
             WHERE user_id = :user_id
             ORDER BY checked ASC, id DESC"
        );

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * Crée un nouveau produit.
     */
    public function create(int $userId, string $nom, string $quantite): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO products (user_id, nom, quantite, checked)
             VALUES (:user_id, :nom, :quantite, 0)"
        );

        $stmt->execute([
            'user_id' => $userId,
            'nom' => $nom,
            'quantite' => $quantite,
        ]);
    }

    /**
     * Supprime un produit par id + user_id.
     * Retourne le nombre de lignes supprimées.
     */
    public function deleteByIdAndUser(int $productId, int $userId): int
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM products
             WHERE id = :id AND user_id = :user_id"
        );

        $stmt->execute([
            'id' => $productId,
            'user_id' => $userId,
        ]);

        return $stmt->rowCount();
    }

    /**
     * Supprime tous les produits d'un utilisateur.
     * Retourne le nombre de lignes supprimées.
     */
    public function deleteAllByUser(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM products
             WHERE user_id = :user_id"
        );

        $stmt->execute(['user_id' => $userId]);

        return $stmt->rowCount();
    }

    /**
     * Met à jour l'état checked d'un produit (id + user_id).
     * Retourne le nombre de lignes modifiées.
     */
    public function updateCheckedByIdAndUser(int $productId, int $userId, bool $checked): int
    {
        $stmt = $this->pdo->prepare(
            "UPDATE products
             SET checked = :checked
             WHERE id = :id AND user_id = :user_id"
        );

        $stmt->execute([
            'checked' => $checked ? 1 : 0,
            'id' => $productId,
            'user_id' => $userId,
        ]);

        return $stmt->rowCount();
    }
}