<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;
use App\Services\SessionService;
use App\Services\DownloadLogService;

class AuditController extends Controller
{
    protected $auditService;
    protected $sessionService;
    protected $downloadLogService;

    public function __construct()
    {
        $this->auditService = app(AuditService::class);
        $this->sessionService = app(SessionService::class);
        $this->downloadLogService = app(DownloadLogService::class);
    }

    /**
     * Display audit logs page
     */
    public function auditLogs(Request $request)
    {
        $action = $request->input('action');
        $userEmail = $request->input('user_email');

        // Get logs based on filters
        if ($action) {
            $logs = $this->auditService->getLogsByAction($action, 200);
        } else {
            $logs = $this->auditService->getRecentLogs(200);
        }

        // Filter by email if provided
        if ($userEmail) {
            $logs = array_filter($logs, function($log) use ($userEmail) {
                return stripos($log['user_email'] ?? '', $userEmail) !== false;
            });
        }

        // Calculate statistics
        $stats = $this->calculateAuditStats($logs);

        return view('superadmin.audit-logs', compact('logs', 'stats'));
    }

    /**
     * Calculate audit statistics
     */
    private function calculateAuditStats(array $logs): array
    {
        $stats = [
            'login_success' => 0,
            'login_failed' => 0,
            'exam_created' => 0,
            'downloads' => 0,
        ];

        foreach ($logs as $log) {
            $action = $log['action'] ?? '';
            
            switch ($action) {
                case 'login_success':
                    $stats['login_success']++;
                    break;
                case 'login_failed':
                    $stats['login_failed']++;
                    break;
                case 'exam_created':
                    $stats['exam_created']++;
                    break;
                case 'past_exam_downloaded':
                case 'marking_guide_downloaded':
                case 'pdf_generated':
                    $stats['downloads']++;
                    break;
            }
        }

        return $stats;
    }

    /**
     * Display active sessions page
     */
    public function activeSessions()
    {
        $sessions = $this->sessionService->getAllActiveSessions(100);
        $stats = $this->sessionService->getSessionStats();

        return view('superadmin.active-sessions', compact('sessions', 'stats'));
    }

    /**
     * Terminate a specific session
     */
    public function terminateSession(string $sessionId)
    {
        $success = $this->sessionService->terminateSession($sessionId);

        if ($success) {
            return back()->with('success', 'Session terminated successfully.');
        }

        return back()->withErrors(['error' => 'Failed to terminate session.']);
    }

    /**
     * Cleanup stale sessions
     */
    public function cleanupSessions()
    {
        $count = $this->sessionService->cleanupStaleSessions();
        
        return back()->with('success', "Cleaned up {$count} stale sessions.");
    }

    /**
     * Display download logs page
     */
    public function downloadLogs(Request $request)
    {
        $downloads = $this->downloadLogService->getRecentDownloads(200);
        $summary = $this->downloadLogService->getDownloadSummary();

        return view('superadmin.download-logs', compact('downloads', 'summary'));
    }

    /**
     * Get user activity report
     */
    public function userActivity(string $userId)
    {
        $auditLogs = $this->auditService->getLogsByUser($userId, 50);
        $downloads = $this->downloadLogService->getDownloadsByUser($userId, 50);
        $sessions = $this->sessionService->getUserActiveSessions($userId);

        return view('superadmin.user-activity', compact('auditLogs', 'downloads', 'sessions', 'userId'));
    }
}
