<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{

    #[Route('/recette', name: 'recipe.index')]
    public function index(Request $request): Response
    {
        return $this->render('recipe/index.html.twig');
    }

    
    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id): Response
    {
        //dd($request->attributes->get('slug'), $request->attributes->getInt('id'));
        //dd($slug, $id); les deux donnes le même résultat
        //return new Response('Recette :' . $slug);
        
        // ça fait a nouveau la même chose :
        //return new JsonResponse([
        //    'slug' => $slug
        //]);

        // return $this->json([
        //     'slug' => $slug
        // ]);

        return $this->render('recipe/show.html.twig', [
            'slug' => $slug,
            'id' => $id
        ]);
    }
}
