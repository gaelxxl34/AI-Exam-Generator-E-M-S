<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Services\FirestoreRestService;

class SessionService
{
    protected FirestoreRestService $firestoreRest;

    public function __construct()
    {
        $this->firestoreRest = app(FirestoreRestService::class);
    }

    /**
     * Track a new session when user logs in
     */
    public function trackSession(string $userId): ?string
    {
        try {
            $sessionData = [
                'user_id' => $userId,
                'user_email' => session('user_email'),
                'user_role' => session('user_role'),
                'user_name' => session('user_firstName'),
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_type' => $this->detectDeviceType(),
                'browser' => $this->detectBrowser(),
                'os' => $this->detectOS(),
                'started_at' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                'last_activity' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                'is_active' => true,
            ];

            $result = $this->firestoreRest->addDocument('ActiveSessions', $sessionData);
            
            session(['active_session_doc_id' => $result['id']]);
            
            Log::info("Session tracked for user: {$userId}", [
                'session_doc_id' => $result['id'],
                'ip' => $sessionData['ip_address'],
            ]);

            return $result['id'];
        } catch (\Exception $e) {
            Log::error("Failed to track session: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update last activity timestamp for the current session
     */
    public function updateActivity(): void
    {
        try {
            $sessionDocId = session('active_session_doc_id');
            if ($sessionDocId) {
                $this->firestoreRest->updateDocument('ActiveSessions', $sessionDocId, [
                    'last_activity' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to update session activity: " . $e->getMessage());
        }
    }

    /**
     * End a session when user logs out
     */
    public function endSession(): void
    {
        try {
            $sessionDocId = session('active_session_doc_id');
            if ($sessionDocId) {
                $this->firestoreRest->updateDocument('ActiveSessions', $sessionDocId, [
                    'is_active' => false,
                    'ended_at' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                ]);
                
                Log::info("Session ended: {$sessionDocId}");
            }
        } catch (\Exception $e) {
            Log::warning("Failed to end session: " . $e->getMessage());
        }
    }

    /**
     * Get all active sessions for a user
     */
    public function getUserActiveSessions(string $userId): array
    {
        try {
            return $this->firestoreRest->runQuery(
                'ActiveSessions',
                [
                    ['field' => 'user_id', 'op' => '==', 'value' => $userId],
                    ['field' => 'is_active', 'op' => '==', 'value' => true],
                ],
                [['field' => 'last_activity', 'direction' => 'DESCENDING']]
            );
        } catch (\Exception $e) {
            Log::error("Failed to get user sessions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all currently active sessions (for admin)
     */
    public function getAllActiveSessions(int $limit = 100): array
    {
        try {
            return $this->firestoreRest->runQuery(
                'ActiveSessions',
                [['field' => 'is_active', 'op' => '==', 'value' => true]],
                [['field' => 'last_activity', 'direction' => 'DESCENDING']],
                $limit
            );
        } catch (\Exception $e) {
            Log::error("Failed to get all active sessions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Terminate a specific session
     */
    public function terminateSession(string $sessionDocId): bool
    {
        try {
            $this->firestoreRest->updateDocument('ActiveSessions', $sessionDocId, [
                'is_active' => false,
                'ended_at' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                'terminated_by' => session('user_email') ?? 'system',
                'termination_reason' => 'manual_termination',
            ]);
            
            Log::info("Session terminated: {$sessionDocId}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to terminate session: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Terminate all sessions for a user
     */
    public function terminateAllUserSessions(string $userId): int
    {
        try {
            $sessions = $this->firestoreRest->runQuery(
                'ActiveSessions',
                [
                    ['field' => 'user_id', 'op' => '==', 'value' => $userId],
                    ['field' => 'is_active', 'op' => '==', 'value' => true],
                ]
            );

            $count = 0;
            foreach ($sessions as $session) {
                $this->terminateSession($session['id']);
                $count++;
            }
            
            Log::info("Terminated {$count} sessions for user: {$userId}");
            return $count;
        } catch (\Exception $e) {
            Log::error("Failed to terminate all user sessions: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Clean up stale sessions (inactive for more than 24 hours)
     */
    public function cleanupStaleSessions(): int
    {
        try {
            $cutoff = (new \DateTime())->modify('-24 hours');
            
            $sessions = $this->firestoreRest->runQuery(
                'ActiveSessions',
                [['field' => 'is_active', 'op' => '==', 'value' => true]]
            );

            $count = 0;
            foreach ($sessions as $data) {
                if (isset($data['last_activity'])) {
                    try {
                        $lastActivity = new \DateTime($data['last_activity']);
                        if ($lastActivity < $cutoff) {
                            $this->firestoreRest->updateDocument('ActiveSessions', $data['id'], [
                                'is_active' => false,
                                'ended_at' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                                'termination_reason' => 'stale_session_cleanup',
                            ]);
                            $count++;
                        }
                    } catch (\Exception $e) {}
                }
            }
            
            Log::info("Cleaned up {$count} stale sessions");
            return $count;
        } catch (\Exception $e) {
            Log::error("Failed to cleanup stale sessions: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get session statistics
     */
    public function getSessionStats(): array
    {
        try {
            $activeSessions = $this->firestoreRest->runQuery(
                'ActiveSessions',
                [['field' => 'is_active', 'op' => '==', 'value' => true]]
            );

            $stats = [
                'total_active' => 0,
                'by_role' => [],
                'by_device' => [],
                'by_browser' => [],
            ];

            foreach ($activeSessions as $data) {
                $stats['total_active']++;

                $role = $data['user_role'] ?? 'unknown';
                $stats['by_role'][$role] = ($stats['by_role'][$role] ?? 0) + 1;

                $device = $data['device_type'] ?? 'unknown';
                $stats['by_device'][$device] = ($stats['by_device'][$device] ?? 0) + 1;

                $browser = $data['browser'] ?? 'unknown';
                $stats['by_browser'][$browser] = ($stats['by_browser'][$browser] ?? 0) + 1;
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error("Failed to get session stats: " . $e->getMessage());
            return ['total_active' => 0];
        }
    }

    /**
     * Detect device type from user agent
     */
    protected function detectDeviceType(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/mobile|android|iphone|ipad|ipod|blackberry|windows phone/i', $userAgent)) {
            if (preg_match('/ipad|tablet/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    protected function detectBrowser(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            if (preg_match('/OPR|Opera/i', $userAgent)) {
                return 'Opera';
            }
            return 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        }
        
        return 'Other';
    }

    /**
     * Detect OS from user agent
     */
    protected function detectOS(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/Windows NT 10/i', $userAgent)) {
            return 'Windows 10/11';
        } elseif (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            return 'iOS';
        } elseif (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        }
        
        return 'Other';
    }
}
