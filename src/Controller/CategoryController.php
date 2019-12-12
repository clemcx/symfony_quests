<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("category/add", name="category_add")
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return Response
     */

    public function add(Request $request , CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        $category = new Category();
        $form =$this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();
            return $this->redirectToRoute('category_add');
        }

        return $this->render('category/add.html.twig', [
                'categories' => $categories,
                'form' => $form->createView(),
            ]
        );
    }

}
