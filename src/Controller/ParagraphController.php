<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Paragraph;
use App\Entity\Story;
use App\Repository\ParagraphRepository;
use App\Service\CacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des paragraphes
 */
#[Route('/stories/{storyId}/paragraphs')]
class ParagraphController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParagraphRepository $paragraphRepository,
        private CacheService $cacheService
    ) {
    }

    /**
     * Liste tous les paragraphes d'une histoire
     */
    #[Route('/', name: 'app_paragraph_index', methods: ['GET'])]
    public function index(int $storyId): Response
    {
        $story = $this->entityManager->getRepository(Story::class)->find($storyId);
        
        if (!$story) {
            throw $this->createNotFoundException('Histoire non trouvée');
        }

        $paragraphs = $this->paragraphRepository->findBy(['story' => $story], ['id' => 'ASC']);

        return $this->render('paragraph/index.html.twig', [
            'story' => $story,
            'paragraphs' => $paragraphs,
        ]);
    }

    /**
     * Crée un nouveau paragraphe
     */
    #[Route('/new', name: 'app_paragraph_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $storyId): Response
    {
        $story = $this->entityManager->getRepository(Story::class)->find($storyId);
        
        if (!$story) {
            throw $this->createNotFoundException('Histoire non trouvée');
        }

        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $isStartParagraph = $request->request->getBoolean('is_start_paragraph');

            if (empty($content)) {
                $this->addFlash('error', 'Le contenu du paragraphe est obligatoire.');
                return $this->redirectToRoute('app_paragraph_new', ['storyId' => $storyId]);
            }

            // Si ce paragraphe est marqué comme paragraphe de départ, 
            // on retire ce statut aux autres paragraphes
            if ($isStartParagraph) {
                $existingStartParagraphs = $this->paragraphRepository->findBy([
                    'story' => $story,
                    'isStartParagraph' => true
                ]);
                
                foreach ($existingStartParagraphs as $existingParagraph) {
                    $existingParagraph->setIsStartParagraph(false);
                }
            }

            $paragraph = new Paragraph();
            $paragraph->setContent($content);
            $paragraph->setIsStartParagraph($isStartParagraph);
            $paragraph->setStory($story);

            $this->entityManager->persist($paragraph);
            $this->entityManager->flush();

            // Invalider le cache de l'histoire
            $this->cacheService->invalidateStoryCache($storyId);

            $this->addFlash('success', 'Paragraphe créé avec succès !');
            return $this->redirectToRoute('app_paragraph_index', ['storyId' => $storyId]);
        }

        return $this->render('paragraph/new.html.twig', [
            'story' => $story,
        ]);
    }

    /**
     * Affiche un paragraphe
     */
    #[Route('/{id}', name: 'app_paragraph_show', methods: ['GET'])]
    public function show(int $storyId, Paragraph $paragraph): Response
    {
        // Vérifier que le paragraphe appartient bien à l'histoire
        if ($paragraph->getStory()->getId() !== $storyId) {
            throw $this->createNotFoundException('Paragraphe non trouvé dans cette histoire');
        }

        return $this->render('paragraph/show.html.twig', [
            'story' => $paragraph->getStory(),
            'paragraph' => $paragraph,
        ]);
    }

    /**
     * Modifie un paragraphe
     */
    #[Route('/{id}/edit', name: 'app_paragraph_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $storyId, Paragraph $paragraph): Response
    {
        // Vérifier que le paragraphe appartient bien à l'histoire
        if ($paragraph->getStory()->getId() !== $storyId) {
            throw $this->createNotFoundException('Paragraphe non trouvé dans cette histoire');
        }

        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $isStartParagraph = $request->request->getBoolean('is_start_paragraph');

            if (empty($content)) {
                $this->addFlash('error', 'Le contenu du paragraphe est obligatoire.');
                return $this->redirectToRoute('app_paragraph_edit', [
                    'storyId' => $storyId,
                    'id' => $paragraph->getId()
                ]);
            }

            // Si ce paragraphe est marqué comme paragraphe de départ, 
            // on retire ce statut aux autres paragraphes
            if ($isStartParagraph && !$paragraph->isStartParagraph()) {
                $existingStartParagraphs = $this->paragraphRepository->findBy([
                    'story' => $paragraph->getStory(),
                    'isStartParagraph' => true
                ]);
                
                foreach ($existingStartParagraphs as $existingParagraph) {
                    $existingParagraph->setIsStartParagraph(false);
                }
            }

            $paragraph->setContent($content);
            $paragraph->setIsStartParagraph($isStartParagraph);

            $this->entityManager->flush();

            // Invalider le cache de l'histoire
            $this->cacheService->invalidateStoryCache($storyId);

            $this->addFlash('success', 'Paragraphe modifié avec succès !');
            return $this->redirectToRoute('app_paragraph_index', ['storyId' => $storyId]);
        }

        return $this->render('paragraph/edit.html.twig', [
            'story' => $paragraph->getStory(),
            'paragraph' => $paragraph,
        ]);
    }

    /**
     * Supprime un paragraphe
     */
    #[Route('/{id}/delete', name: 'app_paragraph_delete', methods: ['POST'])]
    public function delete(Request $request, int $storyId, Paragraph $paragraph): Response
    {
        // Vérifier que le paragraphe appartient bien à l'histoire
        if ($paragraph->getStory()->getId() !== $storyId) {
            throw $this->createNotFoundException('Paragraphe non trouvé dans cette histoire');
        }

        if ($this->isCsrfTokenValid('delete' . $paragraph->getId(), $request->request->get('_token'))) {
            $paragraphId = $paragraph->getId();
            
            $this->entityManager->remove($paragraph);
            $this->entityManager->flush();

            // Invalider le cache de l'histoire
            $this->cacheService->invalidateStoryCache($storyId);

            $this->addFlash('success', 'Paragraphe supprimé avec succès !');
        }

        return $this->redirectToRoute('app_paragraph_index', ['storyId' => $storyId]);
    }
}
