<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DownloadLogService
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = app('firebase.firestore')->database();
    }

    /**
     * Log a file download event
     *
     * @param string $fileType Type of file (past_exam, marking_guide, generated_pdf)
     * @param string $fileId The document ID of the file
     * @param array $details Additional details
     * @return string|null The document ID of the created log
     */
    public function logDownload(string $fileType, string $fileId, array $details = []): ?string
    {
        try {
            $downloadData = [
                'timestamp' => new \DateTime(),
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

            $docRef = $this->firestore->collection('DownloadLogs')->add($downloadData);
            
            // Also increment the download counter on the file document
            $this->incrementDownloadCount($fileType, $fileId);
            
            Log::info("Download logged: {$fileType}", [
                'doc_id' => $docRef->id(),
                'file_id' => $fileId,
                'user' => $downloadData['user_email'],
            ]);

            return $docRef->id();
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
                $docRef = $this->firestore->collection($collection)->document($fileId);
                $doc = $docRef->snapshot();
                
                if ($doc->exists()) {
                    $currentCount = $doc->data()['download_count'] ?? 0;
                    $docRef->update([
                        ['path' => 'download_count', 'value' => $currentCount + 1],
                        ['path' => 'last_downloaded_at', 'value' => new \DateTime()],
                        ['path' => 'last_downloaded_by', 'value' => session('user_email') ?? 'anonymous'],
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
            $logs = $this->firestore->collection('DownloadLogs')
                ->where('file_id', '==', $fileId)
                ->documents();

            $stats = [
                'total_downloads' => 0,
                'unique_users' => [],
                'downloads_by_role' => [],
                'downloads_by_date' => [],
            ];

            foreach ($logs as $log) {
                if ($log->exists()) {
                    $data = $log->data();
                    $stats['total_downloads']++;
                    
                    $userId = $data['user_id'] ?? 'anonymous';
                    if (!in_array($userId, $stats['unique_users'])) {
                        $stats['unique_users'][] = $userId;
                    }

                    $role = $data['user_role'] ?? 'unknown';
                    $stats['downloads_by_role'][$role] = ($stats['downloads_by_role'][$role] ?? 0) + 1;

                    if (isset($data['timestamp'])) {
                        $date = $data['timestamp']->get()->format('Y-m-d');
                        $stats['downloads_by_date'][$date] = ($stats['downloads_by_date'][$date] ?? 0) + 1;
                    }
                }
            }

            $stats['unique_user_count'] = count($stats['unique_users']);
            unset($stats['unique_users']); // Don't expose user list

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
            $logs = $this->firestore->collection('DownloadLogs')
                ->orderBy('timestamp', 'DESC')
                ->limit($limit)
                ->documents();

            $result = [];
            foreach ($logs as $log) {
                if ($log->exists()) {
                    $data = $log->data();
                    $data['id'] = $log->id();
                    $result[] = $data;
                }
            }
            return $result;
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
            $logs = $this->firestore->collection('DownloadLogs')
                ->where('user_id', '==', $userId)
                ->orderBy('timestamp', 'DESC')
                ->limit($limit)
                ->documents();

            $result = [];
            foreach ($logs as $log) {
                if ($log->exists()) {
                    $data = $log->data();
                    $data['id'] = $log->id();
                    $result[] = $data;
                }
            }
            return $result;
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
            // Query DownloadLogs and filter by faculty
            $logs = $this->firestore->collection('DownloadLogs')
                ->orderBy('timestamp', 'DESC')
                ->limit($limit * 3) // Fetch more to ensure we get enough after filtering
                ->documents();

            $result = [];
            $count = 0;

            foreach ($logs as $log) {
                if ($log->exists() && $count < $limit) {
                    $data = $log->data();
                    $logFaculty = $data['faculty'] ?? [];
                    
                    // Check if log belongs to any of the dean's faculties
                    if (!is_array($logFaculty)) {
                        $logFaculty = [$logFaculty];
                    }
                    
                    if (!empty(array_intersect($faculties, $logFaculty))) {
                        $data['id'] = $log->id();
                        // Format timestamp for display
                        if (isset($data['timestamp']) && is_object($data['timestamp'])) {
                            $data['timestamp_formatted'] = $data['timestamp']->get()->format('M d, Y H:i');
                            $data['timestamp_iso'] = $data['timestamp']->get()->format('c');
                        }
                        $result[] = $data;
                        $count++;
                    }
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
            $logs = $this->firestore->collection('DownloadLogs')
                ->orderBy('timestamp', 'DESC')
                ->limit(2000)
                ->documents();

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

            foreach ($logs as $log) {
                if ($log->exists()) {
                    $data = $log->data();
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

                        if (isset($data['timestamp'])) {
                            $timestamp = $data['timestamp']->get();
                            if ($timestamp->format('Y-m-d') === $today->format('Y-m-d')) {
                                $stats['today']++;
                            }
                            if ($timestamp >= $weekAgo) {
                                $stats['this_week']++;
                            }
                            if ($timestamp >= $monthAgo) {
                                $stats['this_month']++;
                            }
                        }
                    }
                }
            }

            // Sort users by download count
            uasort($stats['by_user'], fn($a, $b) => $b['count'] <=> $a['count']);
            $stats['by_user'] = array_slice($stats['by_user'], 0, 10, true); // Top 10

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
            $logs = $this->firestore->collection('DownloadLogs')
                ->orderBy('timestamp', 'DESC')
                ->limit(1000) // Last 1000 downloads for stats
                ->documents();

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

            foreach ($logs as $log) {
                if ($log->exists()) {
                    $data = $log->data();
                    $summary['total_downloads']++;

                    $fileType = $data['file_type'] ?? 'unknown';
                    $summary['by_file_type'][$fileType] = ($summary['by_file_type'][$fileType] ?? 0) + 1;

                    $program = $data['program'] ?? 'unknown';
                    if ($program !== 'unknown') {
                        $summary['by_program'][$program] = ($summary['by_program'][$program] ?? 0) + 1;
                    }

                    if (isset($data['timestamp'])) {
                        $timestamp = $data['timestamp']->get();
                        if ($timestamp->format('Y-m-d') === $today->format('Y-m-d')) {
                            $summary['today']++;
                        }
                        if ($timestamp >= $weekAgo) {
                            $summary['this_week']++;
                        }
                        if ($timestamp >= $monthAgo) {
                            $summary['this_month']++;
                        }
                    }
                }
            }

            return $summary;
        } catch (\Exception $e) {
            Log::error("Failed to get download summary: " . $e->getMessage());
            return ['total_downloads' => 0];
        }
    }
}
