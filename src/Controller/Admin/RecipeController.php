<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Kernel;
use App\Repository\CategoryRepository;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/recette', name: 'admin.recipe.')]
#[IsGranted('ROLE_ADMIN')]
class RecipeController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 2;
        $recipes = $repository->paginateRecipes($page, $limit);
        $maxPage = ceil($recipes->count() / $limit);
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'maxPage' => $maxPage,
            'page' => $page
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
            // $file = $editForm->get('thumbnailFile')->getData();
            // $fileName = $recipe->getId(). '.' . $file->getClientOriginalExtension();
            // $file->move($this->getParameter('kernel.project_dir') . '/public/recettes/images', $fileName);
            // $recipe->setThumbnail($fileName);
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
