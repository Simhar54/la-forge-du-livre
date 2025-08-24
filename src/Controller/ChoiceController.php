<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Choice;
use App\Entity\Paragraph;
use App\Entity\Story;
use App\Repository\ChoiceRepository;
use App\Service\CacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des choix
 */
#[Route('/stories/{storyId}/paragraphs/{paragraphId}/choices')]
class ChoiceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChoiceRepository $choiceRepository,
        private CacheService $cacheService
    ) {
    }

    /**
     * Liste tous les choix d'un paragraphe
     */
    #[Route('/', name: 'app_choice_index', methods: ['GET'])]
    public function index(int $storyId, int $paragraphId): Response
    {
        $story = $this->entityManager->getRepository(Story::class)->find($storyId);
        $paragraph = $this->entityManager->getRepository(Paragraph::class)->find($paragraphId);
        
        if (!$story || !$paragraph) {
            throw $this->createNotFoundException('Histoire ou paragraphe non trouvé');
        }

        // Vérifier que le paragraphe appartient à l'histoire
        if ($paragraph->getStory()->getId() !== $storyId) {
            throw $this->createNotFoundException('Paragraphe non trouvé dans cette histoire');
        }

        $choices = $this->choiceRepository->findBy(['sourceParagraph' => $paragraph], ['id' => 'ASC']);

        return $this->render('choice/index.html.twig', [
            'story' => $story,
            'paragraph' => $paragraph,
            'choices' => $choices,
        ]);
    }

    /**
     * Crée un nouveau choix
     */
    #[Route('/new', name: 'app_choice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $storyId, int $paragraphId): Response
    {
        $story = $this->entityManager->getRepository(Story::class)->find($storyId);
        $paragraph = $this->entityManager->getRepository(Paragraph::class)->find($paragraphId);
        
        if (!$story || !$paragraph) {
            throw $this->createNotFoundException('Histoire ou paragraphe non trouvé');
        }

        // Vérifier que le paragraphe appartient à l'histoire
        if ($paragraph->getStory()->getId() !== $storyId) {
            throw $this->createNotFoundException('Paragraphe non trouvé dans cette histoire');
        }

        if ($request->isMethod('POST')) {
            $text = $request->request->get('text');
            $destinationParagraphId = $request->request->get('destination_paragraph_id');
            $newParagraphContent = $request->request->get('new_paragraph_content');
            $isStartParagraph = $request->request->getBoolean('is_start_paragraph');

            if (empty($text)) {
                $this->addFlash('error', 'Le texte du choix est obligatoire.');
                return $this->redirectToRoute('app_choice_new', [
                    'storyId' => $storyId,
                    'paragraphId' => $paragraphId
                ]);
            }

            $destinationParagraph = null;

            // Vérifier si on crée un nouveau paragraphe ou utilise un existant
            if (!empty($newParagraphContent)) {
                // Créer un nouveau paragraphe
                if (empty($newParagraphContent)) {
                    $this->addFlash('error', 'Le contenu du nouveau paragraphe est obligatoire.');
                    return $this->redirectToRoute('app_choice_new', [
                        'storyId' => $storyId,
                        'paragraphId' => $paragraphId
                    ]);
                }

                // Si ce nouveau paragraphe est marqué comme paragraphe de départ, 
                // on retire ce statut aux autres paragraphes
                if ($isStartParagraph) {
                    $existingStartParagraphs = $this->entityManager->getRepository(Paragraph::class)->findBy([
                        'story' => $story,
                        'isStartParagraph' => true
                    ]);
                    
                    foreach ($existingStartParagraphs as $existingParagraph) {
                        $existingParagraph->setIsStartParagraph(false);
                    }
                }

                $destinationParagraph = new Paragraph();
                $destinationParagraph->setContent($newParagraphContent);
                $destinationParagraph->setIsStartParagraph($isStartParagraph);
                $destinationParagraph->setStory($story);

                $this->entityManager->persist($destinationParagraph);
            } else {
                // Utiliser un paragraphe existant
                if (empty($destinationParagraphId)) {
                    $this->addFlash('error', 'Vous devez soit sélectionner un paragraphe existant, soit créer un nouveau paragraphe.');
                    return $this->redirectToRoute('app_choice_new', [
                        'storyId' => $storyId,
                        'paragraphId' => $paragraphId
                    ]);
                }

                $destinationParagraph = $this->entityManager->getRepository(Paragraph::class)->find($destinationParagraphId);
                if (!$destinationParagraph || $destinationParagraph->getStory()->getId() !== $storyId) {
                    $this->addFlash('error', 'Paragraphe de destination invalide.');
                    return $this->redirectToRoute('app_choice_new', [
                        'storyId' => $storyId,
                        'paragraphId' => $paragraphId
                    ]);
                }
            }

            $choice = new Choice();
            $choice->setText($text);
            $choice->setSourceParagraph($paragraph);
            $choice->setDestinationParagraph($destinationParagraph);

            $this->entityManager->persist($choice);
            $this->entityManager->flush();

            // Invalider le cache de l'histoire
            $this->cacheService->invalidateStoryCache($storyId);

            if (!empty($newParagraphContent)) {
                $this->addFlash('success', 'Choix et nouveau paragraphe créés avec succès !');
                // Rediriger vers la page de l'histoire pour voir le nouveau paragraphe
                return $this->redirectToRoute('app_story_show', [
                    'id' => $storyId
                ]);
            } else {
                $this->addFlash('success', 'Choix créé avec succès !');
                return $this->redirectToRoute('app_choice_index', [
                    'storyId' => $storyId,
                    'paragraphId' => $paragraphId
                ]);
            }
        }

        // Récupérer tous les paragraphes de l'histoire pour le select de destination
        $allParagraphs = $this->entityManager->getRepository(Paragraph::class)->findBy(
            ['story' => $story], 
            ['id' => 'ASC']
        );

        return $this->render('choice/new.html.twig', [
            'story' => $story,
            'paragraph' => $paragraph,
            'allParagraphs' => $allParagraphs,
        ]);
    }

    /**
     * Affiche un choix
     */
    #[Route('/{id}', name: 'app_choice_show', methods: ['GET'])]
    public function show(int $storyId, int $paragraphId, Choice $choice): Response
    {
        // Vérifier que le choix appartient bien au paragraphe et à l'histoire
        if ($choice->getSourceParagraph()->getId() !== $paragraphId || 
            $choice->getSourceParagraph()->getStory()->getId() !== $storyId) {
            throw $this->createNotFoundException('Choix non trouvé');
        }

        return $this->render('choice/show.html.twig', [
            'story' => $choice->getSourceParagraph()->getStory(),
            'paragraph' => $choice->getSourceParagraph(),
            'choice' => $choice,
        ]);
    }

    /**
     * Modifie un choix
     */
    #[Route('/{id}/edit', name: 'app_choice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $storyId, int $paragraphId, Choice $choice): Response
    {
        // Vérifier que le choix appartient bien au paragraphe et à l'histoire
        if ($choice->getSourceParagraph()->getId() !== $paragraphId || 
            $choice->getSourceParagraph()->getStory()->getId() !== $storyId) {
            throw $this->createNotFoundException('Choix non trouvé');
        }

        if ($request->isMethod('POST')) {
            $text = $request->request->get('text');
            $destinationParagraphId = $request->request->get('destination_paragraph_id');

            if (empty($text)) {
                $this->addFlash('error', 'Le texte du choix est obligatoire.');
                return $this->redirectToRoute('app_choice_edit', [
                    'storyId' => $storyId,
                    'paragraphId' => $paragraphId,
                    'id' => $choice->getId()
                ]);
            }

            if (empty($destinationParagraphId)) {
                $this->addFlash('error', 'Le paragraphe de destination est obligatoire.');
                return $this->redirectToRoute('app_choice_edit', [
                    'storyId' => $storyId,
                    'paragraphId' => $paragraphId,
                    'id' => $choice->getId()
                ]);
            }

            $destinationParagraph = $this->entityManager->getRepository(Paragraph::class)->find($destinationParagraphId);
            if (!$destinationParagraph || $destinationParagraph->getStory()->getId() !== $storyId) {
                $this->addFlash('error', 'Paragraphe de destination invalide.');
                return $this->redirectToRoute('app_choice_edit', [
                    'storyId' => $storyId,
                    'paragraphId' => $paragraphId,
                    'id' => $choice->getId()
                ]);
            }

            $choice->setText($text);
            $choice->setDestinationParagraph($destinationParagraph);

            $this->entityManager->flush();

            // Invalider le cache de l'histoire
            $this->cacheService->invalidateStoryCache($storyId);

            $this->addFlash('success', 'Choix modifié avec succès !');
            return $this->redirectToRoute('app_choice_index', [
                'storyId' => $storyId,
                'paragraphId' => $paragraphId
            ]);
        }

        // Récupérer tous les paragraphes de l'histoire pour le select de destination
        $allParagraphs = $this->entityManager->getRepository(Paragraph::class)->findBy(
            ['story' => $choice->getSourceParagraph()->getStory()], 
            ['id' => 'ASC']
        );

        return $this->render('choice/edit.html.twig', [
            'story' => $choice->getSourceParagraph()->getStory(),
            'paragraph' => $choice->getSourceParagraph(),
            'choice' => $choice,
            'allParagraphs' => $allParagraphs,
        ]);
    }

    /**
     * Supprime un choix
     */
    #[Route('/{id}/delete', name: 'app_choice_delete', methods: ['POST'])]
    public function delete(Request $request, int $storyId, int $paragraphId, Choice $choice): Response
    {
        // Vérifier que le choix appartient bien au paragraphe et à l'histoire
        if ($choice->getSourceParagraph()->getId() !== $paragraphId || 
            $choice->getSourceParagraph()->getStory()->getId() !== $storyId) {
            throw $this->createNotFoundException('Choix non trouvé');
        }

        if ($this->isCsrfTokenValid('delete' . $choice->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($choice);
            $this->entityManager->flush();

            // Invalider le cache de l'histoire
            $this->cacheService->invalidateStoryCache($storyId);

            $this->addFlash('success', 'Choix supprimé avec succès !');
        }

        return $this->redirectToRoute('app_choice_index', [
            'storyId' => $storyId,
            'paragraphId' => $paragraphId
        ]);
    }
}
