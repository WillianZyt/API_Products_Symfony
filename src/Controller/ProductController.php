<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use OpenApi\Attributes as AT;

#[Route('/product')]
class ProductController extends AbstractController
{
    // Mostrar todos os produtos
    #[Route('/', name: 'app_all_product_show', methods: ['GET'])]
    #[AT\Get( summary: 'Mostrar todos os produtos')]
    #[AT\Response(
        response: 200, 
        description: 'Success',
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string', 'price' => 'float', 'category' => 'string']
        )
        )]
    #[AT\Tag(name: 'Product')]
    public function showAllProducts(ProductRepository $productRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        if(!$productRepository->findAll()) {
            return $this->json([
                'message' => 'Nenhum produto encontrado!',
            ], 404);
        }
        return $this->json([
            'message' => 'Aqui estão todos os produtos!',
            'data' => $normalizerInterface->normalize($productRepository->findAll(), null, ['groups' => 'products']),
        ]);
    }
    
    // Mostrar um produto
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    #[AT\Get( summary: 'Mostrar um produto')]
    #[AT\Response(
        response: 200, 
        description: 'Success',
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string', 'price' => 'float', 'category' => 'string']
        )
        )]
    #[AT\Tag(name: 'Product')]
    public function showProduct(int $id, ProductRepository $productRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json([
                'message' => 'Produto não encontrado!',
            ], 404);
        }

        return $this->json([
            'message' => 'Aqui está o produto!',
            'data' => $normalizerInterface->normalize($product, null, ['groups' => 'products']),
        ]);
    }

    // Criar um produto
    #[Route('/', name: 'app_product_create', methods: ['POST'])]
    #[AT\Post( summary: 'Criar um produto')]
    #[AT\RequestBody(
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string', 'price' => 'float', 'category' => 'int']
        )
        )]
    #[AT\Tag(name: 'Product')]
    public function create(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository, NormalizerInterface $normalizerInterface): JsonResponse 
    {
        if(!$categoryRepository->findAll()) {
            return $this->json([
                'message' => 'Nenhuma categoria encontrada!',
            ], 404);
        } else if(!$request->getContent()) {
            return $this->json([
                'message' => 'Nenhum dado enviado!',
            ], 400);
        } else if(!json_decode($request->getContent(), true)['name'] || !json_decode($request->getContent(), true)['price'] || !json_decode($request->getContent(), true)['category']) {
            return $this->json([
                'message' => 'Dados incompletos!',
            ], 400);
        }
        $data = json_decode($request->getContent(), true);
        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setCategory($categoryRepository->find($data['category']));

        $productRepository->save($product);

        return $this->json([
            'message' => 'Novo produto criado com sucesso!',
            'data' => $normalizerInterface->normalize($product, null, ['groups' => 'products']),
        ]);
    }

    // Atualizar um produto
    #[Route('/{id}', name: 'app_product_update', methods: ['PUT'])]
    #[AT\Put( summary: 'Atualizar um produto')]
    #[AT\RequestBody(
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string', 'price' => 'float', 'category' => 'int']
        )
        )
    ]
    #[AT\Tag(name: 'Product')]
    public function update(int $id, Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json([
                'message' => 'Produto não encontrado!',
            ], 404);
        } else if(!$request->getContent()) {
            return $this->json([
                'message' => 'Nenhum dado enviado!',
            ], 400);
        } else if(!json_decode($request->getContent(), true)['name'] || !json_decode($request->getContent(), true)['price'] || !json_decode($request->getContent(), true)['category']) {
            return $this->json([
                'message' => 'Dados incompletos!',
            ], 400);
        }

        $data = json_decode($request->getContent(), true);
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setCategory($categoryRepository->find($data['category']));

        $productRepository->save($product);

        return $this->json([
            'message' => 'Produto atualizado com sucesso!',
            'data' => $normalizerInterface->normalize($product, null, ['groups' => 'products']),
        ]);
    }

    // Deletar um produto
    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    #[AT\Delete( summary: 'Deletar um produto')]
    #[AT\Tag(name: 'Product')]
    public function delete(int $id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            return $this->json([
                'message' => 'Produto não encontrado!',
            ], 404);
        }
        $productRepository->delete($product);

        return $this->json([
            'message' => 'Produto deletado com sucesso!',
        ]);
    }

    // Mostrar todos os produtos de uma categoria
    #[Route('/category/{id}', name: 'app_product_category', methods: ['GET'])]
    #[AT\Get( summary: 'Mostrar todos os produtos de uma categoria')]
    #[AT\Tag(name: 'Product')]
    #[AT\Response(
        response: 200, 
        description: 'Success',
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string', 'price' => 'float', 'category' => 'string']
        )
        )]
    public function category(int $id, ProductRepository $productRepository, CategoryRepository $categoryRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->json([
                'message' => 'Categoria não encontrada!',
            ], 404);
        }

        return $this->json([
            'message' => 'Aqui estão os produtos da categoria: ' . $category->getName(),
            'data' => $normalizerInterface->normalize($productRepository->findBy(['category' => $category]), null, ['groups' => 'products']),
        ]);
    }
    
}
