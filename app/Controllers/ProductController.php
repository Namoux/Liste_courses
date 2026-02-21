<?php

declare(strict_types=1);

class ProductController
{
    /**
     * Injection du service métier.
     * Le controller ne fait pas de SQL direct.
     */
    public function __construct(private ProductService $service) {}

    /**
     * Retourne la liste des produits de l'utilisateur connecté.
     */
    public function getProducts(int $userId): array
    {
        return $this->service->list($userId);
    }

    /**
     * Ajoute un produit depuis les données POST.
     */
    public function addProduct(int $userId, array $post): array
    {
        $this->service->add(
            $userId,
            (string)($post['nom'] ?? ''),
            (string)($post['quantite'] ?? '')
        );

        return ['success' => true];
    }

    /**
     * Supprime un produit par son id.
     */
    public function deleteProduct(int $userId, array $post): array
    {
        $id = (int)($post['id'] ?? 0);
        $this->service->delete($userId, $id);

        return ['success' => true];
    }

    /**
     * Supprime tous les produits de l'utilisateur.
     */
    public function deleteAllProducts(int $userId): array
    {
        $this->service->deleteAll($userId);

        return ['success' => true];
    }

    /**
     * Coche/décoche un produit.
     */
    public function toggleProduct(int $userId, array $post): array
    {
        $id = (int)($post['id'] ?? 0);
        $checked = filter_var($post['checked'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $this->service->toggle($userId, $id, $checked);

        return ['success' => true];
    }
}