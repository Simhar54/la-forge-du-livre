<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Service de gestion du cache pour l'application
 */
class CacheService implements CacheClearerInterface
{
    public function __construct(
        private CacheInterface $cache
    ) {
    }

    /**
     * Vide le cache de l'application
     */
    public function clear(string $cacheDir): void
    {
        $this->cache->delete('app_cache');
    }

    /**
     * Invalide le cache des histoires
     */
    public function invalidateStoriesCache(): void
    {
        $this->cache->delete('stories_list');
    }

    /**
     * Invalide le cache d'une histoire spécifique
     */
    public function invalidateStoryCache(int $storyId): void
    {
        $this->cache->delete('story_' . $storyId);
    }
} 