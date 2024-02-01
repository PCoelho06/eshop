<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SearchFilters;
use App\Form\SearchFiltersType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/nos-produits', name: 'app_products')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $search = new SearchFilters();
        $form = $this->createForm(SearchFiltersType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedCategory = $search->getCategories();

            if (count($selectedCategory) > 0) {
                foreach ($selectedCategory as $category) {
                    $categoryId[] = $category->getId();
                }

                $products = $productRepository->findBy(['category' => $categoryId]);

                if (!$products) {
                    $error = "Il n'y a pas de produits correspondant à ces catégories";
                } else $error = null;

            } else {
                if ($products = $productRepository->findAll()) //recupère tous les enregistrements de la table visée
                    $error = null;
                else $error = "Il n'y a pas aucun produit disponible pour le moment";
            }
        } else {
            if ($products = $productRepository->findAll()) //recupère tous les enregistrements de la table visée
                $error = null;
            else $error = "Il n'y a pas de produits disponibles";
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    #[Route('/produit/{slug}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
