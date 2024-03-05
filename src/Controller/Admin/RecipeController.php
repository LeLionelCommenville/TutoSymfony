<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/recette', name: 'admin.recipe.')]
class RecipeController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        $recipes = $repository->findAll();

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    
    // #[Route('/{slug}-{id}', name: 'show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    // public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    // {
    //     dd($request->attributes->get('slug'), $request->attributes->getInt('id'));
    //     dd($slug, $id); les deux donnes le même résultat
    //     return new Response('Recette :' . $slug);
        
    //     ça fait a nouveau la même chose :
    //     return new JsonResponse([
    //        'slug' => $slug
    //     ]);

    //     return $this->json([
    //         'slug' => $slug
    //     ]);
    //     $recipe = $repository->find($id);
    //     if($recipe->getSlug() !== $slug) {
    //         return $this->redirectToRoute('recipe.show', 
    //         ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
    //     }    
    //     return $this->render('recipe/show.html.twig', [
    //         'recipe' => $recipe
    //     ]);
    // }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) {
        $editForm = $this->createForm(RecipeType::class, $recipe);
        $editForm->handleRequest($request);
        if($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();            
            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('admin.recipe.index');
        }
        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'editForm' => $editForm
        ]);
    }

    #[Route('/create', name:'create')]
    public function create(Request $request, EntityManagerInterface $em) {
        $recipe = new Recipe();
        $addForm = $this->createForm(RecipeType::class, $recipe);
        $addForm->handleRequest($request);
        if($addForm->isSubmitted() && $addForm->isValid()) 
        {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été ajoutée');
            return $this->redirectToRoute('admin.recipe.index');
        }
        return $this->render('admin/recipe/create.html.twig', [
            'addForm' => $addForm 
        ]);
    }
    	
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Recipe $recipe, EntityManagerInterface $em) {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('admin.recipe.index');
    }

}
