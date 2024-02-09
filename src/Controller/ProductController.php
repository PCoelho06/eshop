<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Entity\SearchFilters;
use App\Form\SearchFiltersType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        $page = $request->query->getInt('offset', 1);
        $limit = 100;
        $offset = max(1, $page) * $limit - $limit;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedCategory = $search->getCategories();

            if (count($selectedCategory) > 0) {
                foreach ($selectedCategory as $category) {
                    $categoryId[] = $category->getId();
                }

                $products = $productRepository->findByPaginated($offset, $limit);

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

        $total = count($products);
        $pages = ceil($total / $limit);

        $pageRange = 5;
        $proximity = floor($pageRange / 2);
        $startPage = $page - $proximity;
        $endPage = $page + $proximity;
        if ($startPage < 1) {
            $endPage = min($endPage + (1 - $startPage), $pages); // La plus petite valeur
            $startPage = 1;
        }
        if ($endPage > $pages) {
            $startPage = max($startPage - ($endPage - $pages), 1); // La plus grande valeur
            $endPage = $pages;
        }
        $pagesInRange = range($startPage, $endPage, 1); //Crée un tableau contenant un intervalle d'éléments
        if ($page > 1) {
            $previous = $page - 1; // page précédente
        }
        if ($page < $pages) {
            $next = $page + 1; // page suivante
        }
        if (!isset($previous)) $previous = null; // page précédente
        if (!isset($next)) $next = null;

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'pages' => $pages,
            'error' => $error,
            'current' => $page,
            'previous' => $previous,
            'next' => $next,
            'pagesInRange' => $pagesInRange,
            'startPage' => $startPage,
            'endPage' => $endPage

        ]);
    }

    #[Route('/nos-produit/{slug}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/mon-compte/mes-commandes/{slug}/commentaire', name: 'app_product_comment')]
    public function comment(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUser($this->getUser());
            $comment->setProduct($product);
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                'Le commentaire pour le produit ' . $product->getName() . ' a bien été enregistré !'
            );
            return $this->redirectToRoute('product', ['slug' => $product->getSlug()]);
        }
        return $this->render('product/comment.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
