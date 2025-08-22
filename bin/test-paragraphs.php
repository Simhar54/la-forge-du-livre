<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use App\Kernel;
use App\Entity\Story;
use App\Entity\Paragraph;

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env.local');

// Créer le kernel
$kernel = new Kernel('dev', true);
$kernel->boot();

// Récupérer l'EntityManager
$entityManager = $kernel->getContainer()->get('doctrine')->getManager();

echo "🎭 Test de création d'histoire et paragraphes\n";
echo "=============================================\n\n";

try {
    // Créer une histoire de test
    $story = new Story();
    $story->setTitle('L\'Aventure du Chevalier Perdu');
    $story->setDescription('Une histoire interactive où vous incarnez un chevalier qui doit retrouver son chemin dans une forêt mystérieuse.');
    $story->setIsPublished(false);
    
    $entityManager->persist($story);
    $entityManager->flush();
    
    echo "✅ Histoire créée : " . $story->getTitle() . " (ID: " . $story->getId() . ")\n\n";
    
    // Créer le paragraphe de départ
    $startParagraph = new Paragraph();
    $startParagraph->setContent('<h2>Le Réveil</h2><p>Vous vous réveillez dans une clairière mystérieuse. Le soleil commence à se coucher et vous ne reconnaissez pas les lieux. Votre armure est couverte de poussière et votre épée est à vos côtés.</p><p><strong>Que faites-vous ?</strong></p>');
    $startParagraph->setIsStartParagraph(true);
    $startParagraph->setStory($story);
    
    $entityManager->persist($startParagraph);
    
    // Créer un deuxième paragraphe
    $secondParagraph = new Paragraph();
    $secondParagraph->setContent('<h2>La Forêt Sombre</h2><p>Vous vous dirigez vers la forêt. Les arbres sont hauts et leurs branches créent une voûte sombre au-dessus de votre tête. Vous entendez des bruits étranges dans l\'obscurité.</p><p><em>L\'atmosphère est oppressante...</em></p>');
    $secondParagraph->setIsStartParagraph(false);
    $secondParagraph->setStory($story);
    
    $entityManager->persist($secondParagraph);
    
    // Créer un troisième paragraphe
    $thirdParagraph = new Paragraph();
    $thirdParagraph->setContent('<h2>Le Château en Ruines</h2><p>Au lieu d\'entrer dans la forêt, vous décidez d\'explorer les environs. Vous découvrez un ancien château en ruines. Ses murs de pierre grise semblent raconter des histoires oubliées.</p><p><strong>Le château vous attire mystérieusement...</strong></p>');
    $thirdParagraph->setIsStartParagraph(false);
    $thirdParagraph->setStory($story);
    
    $entityManager->persist($thirdParagraph);
    
    $entityManager->flush();
    
    echo "✅ Paragraphes créés :\n";
    echo "   - Paragraphe #" . $startParagraph->getId() . " (Point de départ)\n";
    echo "   - Paragraphe #" . $secondParagraph->getId() . "\n";
    echo "   - Paragraphe #" . $thirdParagraph->getId() . "\n\n";
    
    echo "🎉 Test terminé avec succès !\n";
    echo "Vous pouvez maintenant tester l'interface web :\n";
    echo "http://localhost:8000/stories/" . $story->getId() . "\n";
    echo "http://localhost:8000/stories/" . $story->getId() . "/paragraphs/\n\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
