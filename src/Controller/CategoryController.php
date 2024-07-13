<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as AT;

#[Route('/category')]
class CategoryController extends AbstractController
{
    // Mostrar todas as categorias
    #[Route('/', name: 'app_category_show_all', methods: ['GET'])]
    #[AT\Get( summary: 'Mostrar todas as categorias')]
    #[AT\Tag(name: 'Category')]
    #[AT\Response(
        response: 200, 
        description: 'Success',
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string']
        )
        )]
    public function showAllCategory(CategoryRepository $categoryRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        if(!$categoryRepository->findAll()) {
            return $this->json([
                'message' => 'Nenhuma categoria encontrada!',
            ], 404);
        }
        return $this->json([
            'message' => 'Aqui estão todas as categorias!',
            'data' => $normalizerInterface->normalize($categoryRepository->findAll(), null, ['groups' => 'products']),
        ]);
    }

    // Mostrar uma categoria
    #[Route('/', name: 'app_category_create', methods: ['POST'])]
    #[AT\Post( summary: 'Mostrar uma categoria')]
    #[AT\RequestBody(
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string']
        )
    )]
    #[AT\Tag(name: 'Category')]
    public function create(Request $request, CategoryRepository $categoryRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $category = new Category();
        $category->setName($data['name']);

        $categoryRepository->save($category);
        return $this->json([
            'message' => 'Nova categoria criada com sucesso!',
            'data' => $normalizerInterface->normalize($category, null, ['groups' => 'products']),
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    #[AT\Get( summary: 'Show a category')]
    #[AT\Response(
        response: 200, 
        description: 'Success',
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string']
        )
    )]
    #[AT\Tag(name: 'Category')]
    public function showCategory(int $id, CategoryRepository $categoryRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->json([
                'message' => 'Categoria não encontrada!',
            ], 404);
        }

        return $this->json([
            'message' => 'Aqui está a categoria!',
            'data' => $normalizerInterface->normalize($category, null, ['groups' => 'products']),
        ]);
    }

    // Alterar uma categoria
    #[Route('/{id}', name: 'app_category_update', methods: ['PUT'])]
    #[AT\Put( summary: 'Alterar uma categoria')]
    #[AT\RequestBody(
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string']
        )
    )]
    #[AT\Tag(name: 'Category')]
    public function update(int $id, Request $request, CategoryRepository $categoryRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->json([
                'message' => 'Categoria não encontrada!',
            ], 404);
        }

        $data = json_decode($request->getContent(), true);
        $category->setName($data['name']);

        $categoryRepository->save($category);
        return $this->json([
            'message' => 'Categoria atualizada com sucesso!',
            'data' => $normalizerInterface->normalize($category, null, ['groups' => 'products']),
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    #[AT\Delete( summary: 'Deletar uma categoria')]
    #[AT\Tag(name: 'Category')]
    public function delete(int $id, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->json([
                'message' => 'Categoria não encontrada!',
            ], 404);
        }
        $categoryRepository->delete($category);

        return $this->json([
            'message' => 'Categoria deletada com sucesso!',
        ]);
    }

    // Teste 2
    // Mostrar todos os produtos de uma categoria
    #[Route('/{id}/products', name: 'app_category_products', methods: ['GET'])]
    #[AT\Get( summary: 'Mostrar todos os produtos de uma categoria')]
    #[AT\Tag(name: 'Category')]
    #[AT\Response(
        response: 200, 
        description: 'Success',
        content: new AT\JsonContent(
            type: 'object',
            example: ['name' => 'string', 'price' => 'float', 'category' => 'string']
        )
    )]
    public function products(int $id, CategoryRepository $categoryRepository, NormalizerInterface $normalizerInterface): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            return $this->json([
                'message' => 'Categoria não encontrada!',
            ], 404);
        }

        return $this->json([
            'message' => 'Aqui estão todos os produtos da categoria!',
            'data' => $normalizerInterface->normalize($category->getProducts(), null, ['groups' => 'products']),
        ]);
    }
}
