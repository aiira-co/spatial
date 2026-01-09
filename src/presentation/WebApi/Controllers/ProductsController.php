<?php

declare(strict_types=1);

namespace Presentation\WebApi\Controllers;

use Psr\Http\Message\ResponseInterface;
use Spatial\Core\Attributes\ApiController;
use Spatial\Core\Attributes\Route;
use Spatial\Core\ControllerBase;
use Spatial\Common\HttpAttributes\HttpGet;
use Spatial\Common\HttpAttributes\HttpPost;
use Spatial\Common\HttpAttributes\HttpPut;
use Spatial\Common\HttpAttributes\HttpDelete;
use Spatial\Common\BindSourceAttributes\FromBody;
use Spatial\Common\BindSourceAttributes\FromQuery;

/**
 * Products Controller
 * 
 * Example CRUD controller demonstrating all HTTP verbs and features.
 * 
 * @package Presentation\WebApi\Controllers
 */
#[ApiController]
#[Route('api/[controller]')]
class ProductsController extends ControllerBase
{
    /**
     * Sample products data (replace with service/repository).
     */
    private array $products = [
        ['id' => 1, 'name' => 'Laptop', 'price' => 999.99, 'category' => 'Electronics'],
        ['id' => 2, 'name' => 'Headphones', 'price' => 149.99, 'category' => 'Electronics'],
        ['id' => 3, 'name' => 'Coffee Mug', 'price' => 12.99, 'category' => 'Kitchen'],
    ];

    /**
     * Get all products with optional filtering.
     * 
     * GET /api/products
     * GET /api/products?category=Electronics
     */
    #[HttpGet]
    public function index(
        #[FromQuery] ?string $category = null,
        #[FromQuery] int $page = 1,
        #[FromQuery] int $limit = 10
    ): ResponseInterface {
        $products = $this->products;

        // Filter by category if provided
        if ($category !== null) {
            $products = array_filter(
                $products,
                fn($p) => strtolower($p['category']) === strtolower($category)
            );
        }

        // Paginate
        $total = count($products);
        $offset = ($page - 1) * $limit;
        $products = array_slice(array_values($products), $offset, $limit);

        return $this->ok([
            'data' => $products,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int)ceil($total / $limit),
            ],
        ]);
    }

    /**
     * Get a single product by ID.
     * 
     * GET /api/products/{id}
     */
    #[HttpGet('{id:int}')]
    public function show(int $id): ResponseInterface
    {
        $product = $this->findProduct($id);

        if ($product === null) {
            return $this->notFound("Product with ID {$id} not found");
        }

        return $this->ok($product);
    }

    /**
     * Create a new product.
     * 
     * POST /api/products
     * Body: { "name": "Widget", "price": 29.99, "category": "Gadgets" }
     */
    #[HttpPost]
    public function create(#[FromBody] CreateProductDto $dto): ResponseInterface
    {
        // Validate the DTO
        if ($error = $this->validate($dto)) {
            return $error;
        }

        // Create product (mock - would use repository)
        $product = [
            'id' => count($this->products) + 1,
            'name' => $dto->name,
            'price' => $dto->price,
            'category' => $dto->category ?? 'General',
        ];

        return $this->created($product, "/api/products/{$product['id']}");
    }

    /**
     * Update an existing product.
     * 
     * PUT /api/products/{id}
     * Body: { "name": "Updated Widget", "price": 39.99 }
     */
    #[HttpPut('{id:int}')]
    public function update(int $id, #[FromBody] UpdateProductDto $dto): ResponseInterface
    {
        $product = $this->findProduct($id);

        if ($product === null) {
            return $this->notFound("Product with ID {$id} not found");
        }

        // Update product (mock)
        $product['name'] = $dto->name ?? $product['name'];
        $product['price'] = $dto->price ?? $product['price'];
        $product['category'] = $dto->category ?? $product['category'];

        return $this->ok($product);
    }

    /**
     * Delete a product.
     * 
     * DELETE /api/products/{id}
     */
    #[HttpDelete('{id:int}')]
    public function delete(int $id): ResponseInterface
    {
        $product = $this->findProduct($id);

        if ($product === null) {
            return $this->notFound("Product with ID {$id} not found");
        }

        // Delete would happen here
        return $this->noContent();
    }

    /**
     * Find a product by ID.
     */
    private function findProduct(int $id): ?array
    {
        foreach ($this->products as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }
        return null;
    }
}

/**
 * Create Product DTO
 */
class CreateProductDto
{
    #[\Spatial\Common\ValidationAttributes\Required]
    #[\Spatial\Common\ValidationAttributes\MaxLength(100)]
    public string $name;

    #[\Spatial\Common\ValidationAttributes\Required]
    #[\Spatial\Common\ValidationAttributes\Range(0.01, 99999.99)]
    public float $price;

    #[\Spatial\Common\ValidationAttributes\MaxLength(50)]
    public ?string $category = null;
}

/**
 * Update Product DTO
 */
class UpdateProductDto
{
    #[\Spatial\Common\ValidationAttributes\MaxLength(100)]
    public ?string $name = null;

    #[\Spatial\Common\ValidationAttributes\Range(0.01, 99999.99)]
    public ?float $price = null;

    #[\Spatial\Common\ValidationAttributes\MaxLength(50)]
    public ?string $category = null;
}
