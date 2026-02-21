<?php

declare(strict_types=1);

class ProductService
{
    /**
     * Injection du repository (accès DB).
     */
    public function __construct(private ProductRepository $repo)
    {
    }

    /**
     * Retourne les produits de l'utilisateur.
     */
    public function list(int $userId): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Utilisateur invalide');
        }

        return $this->repo->findAllByUser($userId);
    }

    /**
     * Ajoute un produit après validation métier.
     */
    public function add(int $userId, string $nom, string $quantite): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Utilisateur invalide');
        }

        $nom = trim($nom);
        $quantite = trim($quantite);

        // Nom requis
        if ($nom === '') {
            throw new InvalidArgumentException('nom requis');
        }

        // Quantité optionnelle: vide => 1
        if ($quantite === '') {
            $quantite = '1';
        }
        // Si renseignée, elle doit être un entier positif
        if (!preg_match('/^\d+$/', $quantite) || (int) $quantite <= 0) {
            throw new InvalidArgumentException('quantite doit etre un entier positif');
        }

        $this->repo->create($userId, $nom, $quantite);
    }

    /**
     * Supprime un produit (appartenant à l'utilisateur).
     */
    public function delete(int $userId, int $productId): void
    {
        if ($userId <= 0 || $productId <= 0) {
            throw new InvalidArgumentException('Paramètres invalides');
        }

        $deleted = $this->repo->deleteByIdAndUser($productId, $userId);

        if ($deleted === 0) {
            throw new RuntimeException('Produit introuvable');
        }
    }

    /**
     * Supprime tous les produits de l'utilisateur.
     */
    public function deleteAll(int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Utilisateur invalide');
        }

        $this->repo->deleteAllByUser($userId);
    }

    /**
     * Coche/décoche un produit (appartenant à l'utilisateur).
     */
    public function toggle(int $userId, int $productId, bool $checked): void
    {
        if ($userId <= 0 || $productId <= 0) {
            throw new InvalidArgumentException('Paramètres invalides');
        }

        $updated = $this->repo->updateCheckedByIdAndUser($productId, $userId, $checked);

        if ($updated === 0) {
            throw new RuntimeException('Produit introuvable');
        }
    }
}