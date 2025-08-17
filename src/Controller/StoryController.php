<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Story;
use App\Repository\StoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des histoires
 */
#[Route('/stories')]
class StoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StoryRepository $storyRepository
    ) {
    }

    /**
     * Liste toutes les histoires
     */
    #[Route('/', name: 'app_story_index', methods: ['GET'])]
    public function index(): Response
    {
        $stories = $this->storyRepository->findAll();

        return $this->render('story/index.html.twig', [
            'stories' => $stories,
        ]);
    }

    /**
     * Crée une nouvelle histoire
     */
    #[Route('/new', name: 'app_story_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $description = $request->request->get('description');

            if (empty($title)) {
                $this->addFlash('error', 'Le titre est obligatoire.');
                return $this->redirectToRoute('app_story_new');
            }

            $story = new Story();
            $story->setTitle($title);
            $story->setDescription($description);
            $story->setIsPublished(false);

            $this->entityManager->persist($story);
            $this->entityManager->flush();

            $this->addFlash('success', 'Histoire créée avec succès !');
            return $this->redirectToRoute('app_story_index');
        }

        return $this->render('story/new.html.twig');
    }

    /**
     * Affiche une histoire
     */
    #[Route('/{id}', name: 'app_story_show', methods: ['GET'])]
    public function show(Story $story): Response
    {
        return $this->render('story/show.html.twig', [
            'story' => $story,
        ]);
    }

    /**
     * Modifie le titre d'une histoire
     */
    #[Route('/{id}/edit', name: 'app_story_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Story $story): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $description = $request->request->get('description');

            if (empty($title)) {
                $this->addFlash('error', 'Le titre est obligatoire.');
                return $this->redirectToRoute('app_story_edit', ['id' => $story->getId()]);
            }

            $story->setTitle($title);
            $story->setDescription($description);

            $this->entityManager->flush();

            $this->addFlash('success', 'Histoire modifiée avec succès !');
            return $this->redirectToRoute('app_story_index');
        }

        return $this->render('story/edit.html.twig', [
            'story' => $story,
        ]);
    }

    /**
     * Supprime une histoire
     */
    #[Route('/{id}/delete', name: 'app_story_delete', methods: ['POST'])]
    public function delete(Request $request, Story $story): Response
    {
        if ($this->isCsrfTokenValid('delete' . $story->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($story);
            $this->entityManager->flush();

            $this->addFlash('success', 'Histoire supprimée avec succès !');
        }

        return $this->redirectToRoute('app_story_index');
    }
}
