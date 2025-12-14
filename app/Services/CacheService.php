<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache TTL constants (in seconds)
     */
    const TTL_SHORT = 300;      // 5 minutes
    const TTL_MEDIUM = 1800;    // 30 minutes  
    const TTL_LONG = 3600;      // 1 hour
    const TTL_VERY_LONG = 86400; // 24 hours

    /**
     * Cache key prefixes
     */
    const PREFIX_COURSES = 'courses_';
    const PREFIX_USERS = 'users_';
    const PREFIX_EXAMS = 'exams_';
    const PREFIX_STATS = 'stats_';
    const PREFIX_PAST_EXAMS = 'past_exams_';

    /**
     * Get courses by faculty with caching
     */
    public function getCoursesByFaculty(string $faculty, callable $fetchCallback): array
    {
        $key = self::PREFIX_COURSES . $faculty;
        
        return Cache::remember($key, self::TTL_LONG, function () use ($fetchCallback) {
            Log::info("Cache miss: Fetching courses from Firestore");
            return $fetchCallback();
        });
    }

    /**
     * Get user data with caching
     */
    public function getUserData(string $userId, callable $fetchCallback): ?array
    {
        $key = self::PREFIX_USERS . $userId;
        
        return Cache::remember($key, self::TTL_MEDIUM, function () use ($fetchCallback) {
            Log::info("Cache miss: Fetching user data from Firestore");
            return $fetchCallback();
        });
    }

    /**
     * Get exams by faculty with caching
     */
    public function getExamsByFaculty(string $faculty, callable $fetchCallback): array
    {
        $key = self::PREFIX_EXAMS . $faculty;
        
        return Cache::remember($key, self::TTL_SHORT, function () use ($fetchCallback) {
            Log::info("Cache miss: Fetching exams from Firestore");
            return $fetchCallback();
        });
    }

    /**
     * Get dashboard statistics with caching
     */
    public function getDashboardStats(string $faculty, callable $fetchCallback): array
    {
        $key = self::PREFIX_STATS . 'dashboard_' . $faculty;
        
        return Cache::remember($key, self::TTL_SHORT, function () use ($fetchCallback) {
            Log::info("Cache miss: Calculating dashboard stats");
            return $fetchCallback();
        });
    }

    /**
     * Get past exams metadata with caching
     */
    public function getPastExamsMetadata(string $program, callable $fetchCallback): array
    {
        $key = self::PREFIX_PAST_EXAMS . $program;
        
        return Cache::remember($key, self::TTL_LONG, function () use ($fetchCallback) {
            Log::info("Cache miss: Fetching past exams metadata");
            return $fetchCallback();
        });
    }

    /**
     * Invalidate courses cache for a faculty
     */
    public function invalidateCoursesCache(string $faculty): void
    {
        Cache::forget(self::PREFIX_COURSES . $faculty);
        Log::info("Cache invalidated: courses for {$faculty}");
    }

    /**
     * Invalidate user cache
     */
    public function invalidateUserCache(string $userId): void
    {
        Cache::forget(self::PREFIX_USERS . $userId);
        Log::info("Cache invalidated: user {$userId}");
    }

    /**
     * Invalidate exams cache for a faculty
     */
    public function invalidateExamsCache(string $faculty): void
    {
        Cache::forget(self::PREFIX_EXAMS . $faculty);
        Cache::forget(self::PREFIX_STATS . 'dashboard_' . $faculty);
        Log::info("Cache invalidated: exams for {$faculty}");
    }

    /**
     * Invalidate past exams cache for a program
     */
    public function invalidatePastExamsCache(string $program): void
    {
        Cache::forget(self::PREFIX_PAST_EXAMS . $program);
        Log::info("Cache invalidated: past exams for {$program}");
    }

    /**
     * Invalidate all caches for a faculty (useful after bulk operations)
     */
    public function invalidateFacultyCache(string $faculty): void
    {
        $this->invalidateCoursesCache($faculty);
        $this->invalidateExamsCache($faculty);
        Log::info("Cache invalidated: all caches for faculty {$faculty}");
    }

    /**
     * Clear all application caches
     */
    public function clearAllCaches(): void
    {
        Cache::flush();
        Log::info("Cache cleared: all application caches");
    }

    /**
     * Generic remember function for custom caching
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Generic forget function
     */
    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Check if a key exists in cache
     */
    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Get a value from cache without default
     */
    public function get(string $key)
    {
        return Cache::get($key);
    }

    /**
     * Put a value in cache
     */
    public function put(string $key, $value, int $ttl = self::TTL_MEDIUM): void
    {
        Cache::put($key, $value, $ttl);
    }
}
