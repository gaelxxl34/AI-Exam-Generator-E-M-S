<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Services\FirestoreRestService;

class DownloadLogService
{
    protected FirestoreRestService $firestoreRest;

    public function __construct()
    {
        $this->firestoreRest = app(FirestoreRestService::class);
    }

    /**
     * Log a file download event
     */
    public function logDownload(string $fileType, string $fileId, array $details = []): ?string
    {
        try {
            $downloadData = [
                'timestamp' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                'user_id' => session('user') ?? 'anonymous',
                'user_email' => session('user_email') ?? 'anonymous',
                'user_role' => session('user_role') ?? 'unknown',
                'user_name' => session('user_firstName') ?? 'Unknown',
                'faculty' => session('user_faculty') ?? [],
                'file_type' => $fileType,
                'file_id' => $fileId,
                'file_name' => $details['file_name'] ?? null,
                'course_unit' => $details['course_unit'] ?? null,
                'program' => $details['program'] ?? null,
                'year' => $details['year'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
            ];

            $result = $this->firestoreRest->addDocument('DownloadLogs', $downloadData);
            
            // Also increment the download counter on the file document
            $this->incrementDownloadCount($fileType, $fileId);
            
            Log::info("Download logged: {$fileType}", [
                'doc_id' => $result['id'],
                'file_id' => $fileId,
                'user' => $downloadData['user_email'],
            ]);

            return $result['id'];
        } catch (\Exception $e) {
            Log::error("Failed to log download: " . $e->getMessage(), [
                'file_type' => $fileType,
                'file_id' => $fileId,
            ]);
            return null;
        }
    }

    /**
     * Increment download count on the file document
     */
    protected function incrementDownloadCount(string $fileType, string $fileId): void
    {
        try {
            $collection = match($fileType) {
                'past_exam' => 'pastExams',
                'exam', 'marking_guide' => 'Exams',
                default => null,
            };

            if ($collection) {
                $doc = $this->firestoreRest->getDocument($collection, $fileId);
                
                if ($doc) {
                    $currentCount = $doc['download_count'] ?? 0;
                    $this->firestoreRest->updateDocument($collection, $fileId, [
                        'download_count' => $currentCount + 1,
                        'last_downloaded_at' => (new \DateTime())->format('Y-m-d\TH:i:s.u\Z'),
                        'last_downloaded_by' => session('user_email') ?? 'anonymous',
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to increment download count: " . $e->getMessage());
        }
    }

    /**
     * Log past exam download
     */
    public function logPastExamDownload(string $examId, string $courseUnit, string $program, string $year): ?string
    {
        return $this->logDownload('past_exam', $examId, [
            'course_unit' => $courseUnit,
            'program' => $program,
            'year' => $year,
            'file_name' => "{$courseUnit}_{$year}.pdf",
        ]);
    }

    /**
     * Log marking guide download
     */
    public function logMarkingGuideDownload(string $examId, string $courseUnit): ?string
    {
        return $this->logDownload('marking_guide', $examId, [
            'course_unit' => $courseUnit,
            'file_name' => "{$courseUnit}_marking_guide",
        ]);
    }

    /**
     * Log generated PDF download
     */
    public function logGeneratedPdfDownload(string $courseUnit, string $faculty): ?string
    {
        return $this->logDownload('generated_pdf', uniqid(), [
            'course_unit' => $courseUnit,
            'file_name' => "{$courseUnit}_exam.pdf",
            'faculty' => $faculty,
        ]);
    }

    /**
     * Log PDF preview/view
     */
    public function logPdfView(string $examId, string $courseUnit): ?string
    {
        return $this->logDownload('pdf_preview', $examId, [
            'course_unit' => $courseUnit,
            'file_name' => "{$courseUnit}_preview.pdf",
        ]);
    }

    /**
     * Get download statistics for a file
     */
    public function getFileDownloadStats(string $fileId): array
    {
        try {
            $logs = $this->firestoreRest->queryCollection('DownloadLogs', 'file_id', '==', $fileId);

            $stats = [
                'total_downloads' => 0,
                'unique_users' => [],
                'downloads_by_role' => [],
                'downloads_by_date' => [],
            ];

            foreach ($logs as $data) {
                $stats['total_downloads']++;
                
                $userId = $data['user_id'] ?? 'anonymous';
                if (!in_array($userId, $stats['unique_users'])) {
                    $stats['unique_users'][] = $userId;
                }

                $role = $data['user_role'] ?? 'unknown';
                $stats['downloads_by_role'][$role] = ($stats['downloads_by_role'][$role] ?? 0) + 1;

                if (isset($data['timestamp']) && is_string($data['timestamp'])) {
                    try {
                        $date = (new \DateTime($data['timestamp']))->format('Y-m-d');
                        $stats['downloads_by_date'][$date] = ($stats['downloads_by_date'][$date] ?? 0) + 1;
                    } catch (\Exception $e) {}
                }
            }

            $stats['unique_user_count'] = count($stats['unique_users']);
            unset($stats['unique_users']);

            return $stats;
        } catch (\Exception $e) {
            Log::error("Failed to get download stats: " . $e->getMessage());
            return ['total_downloads' => 0, 'unique_user_count' => 0];
        }
    }

    /**
     * Get recent downloads (for admin dashboard)
     */
    public function getRecentDownloads(int $limit = 50): array
    {
        try {
            return $this->firestoreRest->runQuery(
                'DownloadLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], $limit
            );
        } catch (\Exception $e) {
            Log::error("Failed to fetch recent downloads: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get downloads by user
     */
    public function getDownloadsByUser(string $userId, int $limit = 50): array
    {
        try {
            return $this->firestoreRest->runQuery(
                'DownloadLogs',
                [['field' => 'user_id', 'op' => '==', 'value' => $userId]],
                [['field' => 'timestamp', 'direction' => 'DESCENDING']],
                $limit
            );
        } catch (\Exception $e) {
            Log::error("Failed to fetch user downloads: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get downloads filtered by faculty (for Dean dashboard)
     */
    public function getDownloadsByFaculty(array $faculties, int $limit = 100): array
    {
        try {
            $logs = $this->firestoreRest->runQuery(
                'DownloadLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], $limit * 3
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
            Log::error("Failed to fetch downloads by faculty: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get download statistics for faculty (for Dean dashboard)
     */
    public function getFacultyDownloadStats(array $faculties): array
    {
        try {
            $logs = $this->firestoreRest->runQuery(
                'DownloadLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], 2000
            );

            $stats = [
                'total_downloads' => 0,
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0,
                'by_file_type' => [],
                'by_user' => [],
                'recent_activity' => [],
            ];

            $today = new \DateTime();
            $weekAgo = (new \DateTime())->modify('-7 days');
            $monthAgo = (new \DateTime())->modify('-30 days');

            foreach ($logs as $data) {
                $logFaculty = $data['faculty'] ?? [];
                if (!is_array($logFaculty)) {
                    $logFaculty = [$logFaculty];
                }

                if (!empty(array_intersect($faculties, $logFaculty))) {
                    $stats['total_downloads']++;

                    $fileType = $data['file_type'] ?? 'unknown';
                    $stats['by_file_type'][$fileType] = ($stats['by_file_type'][$fileType] ?? 0) + 1;

                    $userEmail = $data['user_email'] ?? 'anonymous';
                    if (!isset($stats['by_user'][$userEmail])) {
                        $stats['by_user'][$userEmail] = [
                            'count' => 0,
                            'name' => $data['user_name'] ?? 'Unknown',
                            'role' => $data['user_role'] ?? 'unknown',
                        ];
                    }
                    $stats['by_user'][$userEmail]['count']++;

                    if (isset($data['timestamp']) && is_string($data['timestamp'])) {
                        try {
                            $timestamp = new \DateTime($data['timestamp']);
                            if ($timestamp->format('Y-m-d') === $today->format('Y-m-d')) {
                                $stats['today']++;
                            }
                            if ($timestamp >= $weekAgo) {
                                $stats['this_week']++;
                            }
                            if ($timestamp >= $monthAgo) {
                                $stats['this_month']++;
                            }
                        } catch (\Exception $e) {}
                    }
                }
            }

            uasort($stats['by_user'], fn($a, $b) => $b['count'] <=> $a['count']);
            $stats['by_user'] = array_slice($stats['by_user'], 0, 10, true);

            return $stats;
        } catch (\Exception $e) {
            Log::error("Failed to get faculty download stats: " . $e->getMessage());
            return ['total_downloads' => 0];
        }
    }

    /**
     * Get download statistics summary
     */
    public function getDownloadSummary(): array
    {
        try {
            $logs = $this->firestoreRest->runQuery(
                'DownloadLogs', [], [['field' => 'timestamp', 'direction' => 'DESCENDING']], 1000
            );

            $summary = [
                'total_downloads' => 0,
                'by_file_type' => [],
                'by_faculty' => [],
                'by_program' => [],
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0,
            ];

            $today = new \DateTime();
            $weekAgo = (new \DateTime())->modify('-7 days');
            $monthAgo = (new \DateTime())->modify('-30 days');

            foreach ($logs as $data) {
                $summary['total_downloads']++;

                $fileType = $data['file_type'] ?? 'unknown';
                $summary['by_file_type'][$fileType] = ($summary['by_file_type'][$fileType] ?? 0) + 1;

                $program = $data['program'] ?? 'unknown';
                if ($program !== 'unknown') {
                    $summary['by_program'][$program] = ($summary['by_program'][$program] ?? 0) + 1;
                }

                if (isset($data['timestamp']) && is_string($data['timestamp'])) {
                    try {
                        $timestamp = new \DateTime($data['timestamp']);
                        if ($timestamp->format('Y-m-d') === $today->format('Y-m-d')) {
                            $summary['today']++;
                        }
                        if ($timestamp >= $weekAgo) {
                            $summary['this_week']++;
                        }
                        if ($timestamp >= $monthAgo) {
                            $summary['this_month']++;
                        }
                    } catch (\Exception $e) {}
                }
            }

            return $summary;
        } catch (\Exception $e) {
            Log::error("Failed to get download summary: " . $e->getMessage());
            return ['total_downloads' => 0];
        }
    }
}
