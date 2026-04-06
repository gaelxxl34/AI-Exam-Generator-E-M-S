<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Services\FirestoreRestService;

class AuditService
{
    protected FirestoreRestService $firestoreRest;

    public function __construct()
    {
        $this->firestoreRest = app(FirestoreRestService::class);
    }

    /**
     * Log an audit event to Firestore
     */
    public function log(string $action, array $details = []): ?string
    {
        try {
            $auditData = [
                'timestamp' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                'user_id' => session('user') ?? 'anonymous',
                'user_email' => session('user_email') ?? 'anonymous',
                'user_role' => session('user_role') ?? 'unknown',
                'user_name' => session('user_firstName') ?? 'Unknown',
                'faculty' => session('user_faculty') ?? [],
                'action' => $action,
                'resource_type' => $details['resource_type'] ?? null,
                'resource_id' => $details['resource_id'] ?? null,
                'resource_name' => $details['resource_name'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method(),
                'details' => $details,
            ];

            $result = $this->firestoreRest->addDocument('AuditLogs', $auditData);
            
            Log::info("Audit log created: {$action}", [
                'doc_id' => $result['id'],
                'user' => $auditData['user_email'],
            ]);

            return $result['id'];
        } catch (\Exception $e) {
            Log::error("Failed to create audit log: " . $e->getMessage(), [
                'action' => $action,
                'details' => $details,
            ]);
            return null;
        }
    }

    /**
     * Log authentication events
     */
    public function logLogin(bool $success, string $email, string $failureReason = null): ?string
    {
        return $this->log($success ? 'login_success' : 'login_failed', [
            'resource_type' => 'authentication',
            'email_attempted' => $email,
            'success' => $success,
            'failure_reason' => $failureReason,
        ]);
    }

    public function logLogout(): ?string
    {
        return $this->log('logout', [
            'resource_type' => 'authentication',
        ]);
    }

    public function logPasswordResetRequest(string $email): ?string
    {
        return $this->log('password_reset_request', [
            'resource_type' => 'authentication',
            'email_requested' => $email,
        ]);
    }

    /**
     * Log exam operations
     */
    public function logExamCreated(string $examId, string $courseUnit, string $faculty): ?string
    {
        return $this->log('exam_created', [
            'resource_type' => 'exam',
            'resource_id' => $examId,
            'resource_name' => $courseUnit,
            'faculty' => $faculty,
        ]);
    }

    public function logExamUpdated(string $examId, string $courseUnit, array $changes = []): ?string
    {
        return $this->log('exam_updated', [
            'resource_type' => 'exam',
            'resource_id' => $examId,
            'resource_name' => $courseUnit,
            'changes' => $changes,
        ]);
    }

    public function logExamDeleted(string $examId, string $courseUnit): ?string
    {
        return $this->log('exam_deleted', [
            'resource_type' => 'exam',
            'resource_id' => $examId,
            'resource_name' => $courseUnit,
        ]);
    }

    public function logQuestionAdded(string $examId, string $section, int $questionIndex): ?string
    {
        return $this->log('question_added', [
            'resource_type' => 'exam_question',
            'resource_id' => $examId,
            'section' => $section,
            'question_index' => $questionIndex,
        ]);
    }

    public function logQuestionEdited(string $examId, string $section, int $questionIndex): ?string
    {
        return $this->log('question_edited', [
            'resource_type' => 'exam_question',
            'resource_id' => $examId,
            'section' => $section,
            'question_index' => $questionIndex,
        ]);
    }

    public function logQuestionDeleted(string $examId, string $section, int $questionIndex): ?string
    {
        return $this->log('question_deleted', [
            'resource_type' => 'exam_question',
            'resource_id' => $examId,
            'section' => $section,
            'question_index' => $questionIndex,
        ]);
    }

    /**
     * Log approval workflow
     */
    public function logExamApproved(string $examId, string $courseUnit): ?string
    {
        return $this->log('exam_approved', [
            'resource_type' => 'exam',
            'resource_id' => $examId,
            'resource_name' => $courseUnit,
        ]);
    }

    public function logExamDeclined(string $examId, string $courseUnit, string $reason): ?string
    {
        return $this->log('exam_declined', [
            'resource_type' => 'exam',
            'resource_id' => $examId,
            'resource_name' => $courseUnit,
            'decline_reason' => $reason,
        ]);
    }

    /**
     * Log file operations
     */
    public function logPastExamUploaded(string $examId, string $courseUnit, string $program, string $year): ?string
    {
        return $this->log('past_exam_uploaded', [
            'resource_type' => 'past_exam',
            'resource_id' => $examId,
            'resource_name' => $courseUnit,
            'program' => $program,
            'year' => $year,
        ]);
    }

    public function logPastExamDeleted(string $examId, string $courseUnit = null): ?string
    {
        return $this->log('past_exam_deleted', [
            'resource_type' => 'past_exam',
            'resource_id' => $examId,
            'resource_name' => $courseUnit,
        ]);
    }

    public function logPdfGenerated(string $courseUnit, string $faculty): ?string
    {
        return $this->log('pdf_generated', [
            'resource_type' => 'generated_pdf',
            'resource_name' => $courseUnit,
            'faculty' => $faculty,
        ]);
    }

    public function logMarkingGuideDownloaded(string $examId, string $courseUnit): ?string
    {
        return $this->log('marking_guide_downloaded', [
            'resource_type' => 'marking_guide',
            'resource_id' => $examId,
            'resource_name' => $courseUnit,
        ]);
    }

    /**
     * Log user management
     */
    public function logUserCreated(string $userId, string $email, string $role): ?string
    {
        return $this->log('user_created', [
            'resource_type' => 'user',
            'resource_id' => $userId,
            'resource_name' => $email,
            'target_role' => $role,
        ]);
    }

    public function logUserUpdated(string $userId, string $email, array $changes = []): ?string
    {
        return $this->log('user_updated', [
            'resource_type' => 'user',
            'resource_id' => $userId,
            'resource_name' => $email,
            'changes' => $changes,
        ]);
    }

    public function logUserDeleted(string $userId, string $email): ?string
    {
        return $this->log('user_deleted', [
            'resource_type' => 'user',
            'resource_id' => $userId,
            'resource_name' => $email,
        ]);
    }

    public function logUserDisabled(string $userId, string $email): ?string
    {
        return $this->log('user_disabled', [
            'resource_type' => 'user',
            'resource_id' => $userId,
            'resource_name' => $email,
        ]);
    }

    public function logUserEnabled(string $userId, string $email): ?string
    {
        return $this->log('user_enabled', [
            'resource_type' => 'user',
            'resource_id' => $userId,
            'resource_name' => $email,
        ]);
    }

    public function logCoursesAssigned(string $userId, string $email, array $courses): ?string
    {
        return $this->log('courses_assigned', [
            'resource_type' => 'user',
            'resource_id' => $userId,
            'resource_name' => $email,
            'courses' => $courses,
        ]);
    }

    /**
     * Log admin operations
     */
    public function logBulkArchive(int $count, string $archiveName): ?string
    {
        return $this->log('bulk_archive', [
            'resource_type' => 'system',
            'archive_name' => $archiveName,
            'items_archived' => $count,
        ]);
    }

    public function logBulkDelete(int $count, string $collectionName): ?string
    {
        return $this->log('bulk_delete', [
            'resource_type' => 'system',
            'collection' => $collectionName,
            'items_deleted' => $count,
        ]);
    }

    /**
     * Log unauthorized access attempts
     */
    public function logUnauthorizedAccess(string $attemptedUrl, string $requiredRole): ?string
    {
        return $this->log('unauthorized_access_attempt', [
            'resource_type' => 'security',
            'attempted_url' => $attemptedUrl,
            'required_role' => $requiredRole,
        ]);
    }

    /**
     * Query audit logs (for admin viewing)
     */
    public function getRecentLogs(int $limit = 100): array
    {
        try {
            return $this->firestoreRest->runQuery(
                'AuditLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], $limit
            );
        } catch (\Exception $e) {
            Log::error("Failed to fetch audit logs: " . $e->getMessage());
            return [];
        }
    }

    public function getLogsByUser(string $userId, int $limit = 50): array
    {
        try {
            return $this->firestoreRest->runQuery(
                'AuditLogs',
                [['field' => 'user_id', 'op' => '==', 'value' => $userId]],
                [['field' => 'timestamp', 'direction' => 'DESCENDING']],
                $limit
            );
        } catch (\Exception $e) {
            Log::error("Failed to fetch user audit logs: " . $e->getMessage());
            return [];
        }
    }

    public function getLogsByAction(string $action, int $limit = 50): array
    {
        try {
            return $this->firestoreRest->runQuery(
                'AuditLogs',
                [['field' => 'action', 'op' => '==', 'value' => $action]],
                [['field' => 'timestamp', 'direction' => 'DESCENDING']],
                $limit
            );
        } catch (\Exception $e) {
            Log::error("Failed to fetch audit logs by action: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get audit logs filtered by faculty (for Dean dashboard)
     */
    public function getLogsByFaculty(array $faculties, int $limit = 100, ?string $actionFilter = null): array
    {
        try {
            $logs = $this->firestoreRest->runQuery(
                'AuditLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], $limit * 3
            );

            $result = [];
            $count = 0;

            foreach ($logs as $data) {
                if ($count >= $limit) break;

                $logFaculty = $data['faculty'] ?? [];
                if (!is_array($logFaculty)) {
                    $logFaculty = [$logFaculty];
                }

                if (!empty(array_intersect($faculties, $logFaculty))) {
                    if ($actionFilter === null || ($data['action'] ?? '') === $actionFilter) {
                        if (isset($data['timestamp']) && is_string($data['timestamp'])) {
                            try {
                                $ts = new \DateTime($data['timestamp']);
                                $data['timestamp_formatted'] = $ts->format('M d, Y H:i');
                                $data['timestamp_iso'] = $ts->format('c');
                            } catch (\Exception $e) {}
                        }
                        $result[] = $data;
                        $count++;
                    }
                }
            }
            return $result;
        } catch (\Exception $e) {
            Log::error("Failed to fetch audit logs by faculty: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get security-related audit logs for faculty
     */
    public function getFacultySecurityLogs(array $faculties, int $limit = 50): array
    {
        try {
            $securityActions = [
                'login_success', 'login_failed', 'logout',
                'unauthorized_access_attempt', 'password_reset_request'
            ];

            $logs = $this->firestoreRest->runQuery(
                'AuditLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], $limit * 5
            );

            $result = [];
            $count = 0;

            foreach ($logs as $data) {
                if ($count >= $limit) break;

                $logFaculty = $data['faculty'] ?? [];
                $action = $data['action'] ?? '';
                if (!is_array($logFaculty)) {
                    $logFaculty = [$logFaculty];
                }

                if (!empty(array_intersect($faculties, $logFaculty)) && in_array($action, $securityActions)) {
                    if (isset($data['timestamp']) && is_string($data['timestamp'])) {
                        try {
                            $ts = new \DateTime($data['timestamp']);
                            $data['timestamp_formatted'] = $ts->format('M d, Y H:i');
                            $data['timestamp_iso'] = $ts->format('c');
                        } catch (\Exception $e) {}
                    }
                    $result[] = $data;
                    $count++;
                }
            }
            return $result;
        } catch (\Exception $e) {
            Log::error("Failed to fetch faculty security logs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get exam-related activity for faculty
     */
    public function getFacultyExamActivity(array $faculties, int $limit = 50): array
    {
        try {
            $examActions = [
                'exam_created', 'exam_updated', 'exam_approved', 'exam_declined',
                'question_added', 'question_edited', 'question_deleted',
                'dean_question_edit', 'pdf_generated', 'marking_guide_downloaded',
            ];

            $logs = $this->firestoreRest->runQuery(
                'AuditLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], $limit * 5
            );

            $result = [];
            $count = 0;

            foreach ($logs as $data) {
                if ($count >= $limit) break;

                $logFaculty = $data['faculty'] ?? [];
                $action = $data['action'] ?? '';
                if (!is_array($logFaculty)) {
                    $logFaculty = [$logFaculty];
                }

                if (!empty(array_intersect($faculties, $logFaculty)) && in_array($action, $examActions)) {
                    if (isset($data['timestamp']) && is_string($data['timestamp'])) {
                        try {
                            $ts = new \DateTime($data['timestamp']);
                            $data['timestamp_formatted'] = $ts->format('M d, Y H:i');
                            $data['timestamp_iso'] = $ts->format('c');
                        } catch (\Exception $e) {}
                    }
                    $result[] = $data;
                    $count++;
                }
            }
            return $result;
        } catch (\Exception $e) {
            Log::error("Failed to fetch faculty exam activity: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get faculty activity statistics for dashboard
     */
    public function getFacultyActivityStats(array $faculties): array
    {
        try {
            $logs = $this->firestoreRest->runQuery(
                'AuditLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], 2000
            );

            $stats = [
                'total_actions' => 0,
                'today' => 0,
                'this_week' => 0,
                'login_success' => 0,
                'login_failed' => 0,
                'exams_created' => 0,
                'exams_approved' => 0,
                'exams_declined' => 0,
                'questions_edited' => 0,
                'pdfs_generated' => 0,
                'active_users' => [],
            ];

            $today = new \DateTime();
            $weekAgo = (new \DateTime())->modify('-7 days');

            foreach ($logs as $data) {
                $logFaculty = $data['faculty'] ?? [];
                if (!is_array($logFaculty)) {
                    $logFaculty = [$logFaculty];
                }

                if (!empty(array_intersect($faculties, $logFaculty))) {
                    $stats['total_actions']++;
                    $action = $data['action'] ?? '';
                    $userEmail = $data['user_email'] ?? 'anonymous';

                    switch ($action) {
                        case 'login_success': $stats['login_success']++; break;
                        case 'login_failed': $stats['login_failed']++; break;
                        case 'exam_created': $stats['exams_created']++; break;
                        case 'exam_approved': $stats['exams_approved']++; break;
                        case 'exam_declined': $stats['exams_declined']++; break;
                        case 'question_edited':
                        case 'dean_question_edit': $stats['questions_edited']++; break;
                        case 'pdf_generated': $stats['pdfs_generated']++; break;
                    }

                    if (!isset($stats['active_users'][$userEmail])) {
                        $stats['active_users'][$userEmail] = [
                            'name' => $data['user_name'] ?? 'Unknown',
                            'role' => $data['user_role'] ?? 'unknown',
                            'actions' => 0,
                        ];
                    }
                    $stats['active_users'][$userEmail]['actions']++;

                    if (isset($data['timestamp']) && is_string($data['timestamp'])) {
                        try {
                            $timestamp = new \DateTime($data['timestamp']);
                            if ($timestamp->format('Y-m-d') === $today->format('Y-m-d')) {
                                $stats['today']++;
                            }
                            if ($timestamp >= $weekAgo) {
                                $stats['this_week']++;
                            }
                        } catch (\Exception $e) {}
                    }
                }
            }

            uasort($stats['active_users'], fn($a, $b) => $b['actions'] <=> $a['actions']);
            $stats['active_users'] = array_slice($stats['active_users'], 0, 10, true);

            return $stats;
        } catch (\Exception $e) {
            Log::error("Failed to get faculty activity stats: " . $e->getMessage());
            return ['total_actions' => 0];
        }
    }
}
