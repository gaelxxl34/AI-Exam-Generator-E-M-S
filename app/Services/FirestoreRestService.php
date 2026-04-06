<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirestoreRestService
{
    private $projectId;
    private $accessToken;
    private $accessTokenExpiry;
    private $serviceAccount;
    private $baseUrl;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->loadServiceAccount();
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    private function loadServiceAccount()
    {
        if (env('FIREBASE_CREDENTIALS_BASE64')) {
            $this->serviceAccount = json_decode(base64_decode(env('FIREBASE_CREDENTIALS_BASE64')), true);
        } else {
            $credPath = env('FIREBASE_CREDENTIALS');
            if (!$credPath || !file_exists($credPath)) {
                $credPath = base_path('firebase_credentials.json');
            }
            $this->serviceAccount = json_decode(file_get_contents($credPath), true);
        }
    }

    // ─── READ OPERATIONS ─────────────────────────────────────────

    public function getDocument($collection, $documentId)
    {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->timeout(30)->get($url);

        if ($response->status() === 404) {
            return null;
        }

        if (!$response->successful()) {
            throw new \Exception('Firestore request failed: ' . $response->body());
        }

        return $this->convertFirestoreDocument($response->json());
    }

    public function getCollection($collection)
    {
        $url = "{$this->baseUrl}/{$collection}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception('Firestore collection request failed: ' . $response->body());
        }

        $documents = $response->json()['documents'] ?? [];
        $result = [];

        foreach ($documents as $doc) {
            $converted = $this->convertFirestoreDocument($doc);
            if ($converted) {
                $result[] = $converted;
            }
        }

        return $result;
    }

    /**
     * Run a structured query with a single field filter.
     */
    public function queryCollection($collection, $field, $operator, $value)
    {
        return $this->runQuery($collection, [
            ['field' => $field, 'op' => $operator, 'value' => $value],
        ]);
    }

    /**
     * Run a structured query with multiple filters, ordering, and limit.
     *
     * @param string $collection
     * @param array  $filters   [['field'=>…,'op'=>…,'value'=>…], …]
     * @param array  $orderBy   [['field'=>…,'direction'=>'ASCENDING'|'DESCENDING'], …]
     * @param int|null $limit
     * @return array
     */
    public function runQuery(string $collection, array $filters = [], array $orderBy = [], ?int $limit = null): array
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";

        $structuredQuery = [
            'from' => [['collectionId' => $collection]],
        ];

        // Build WHERE clause
        if (count($filters) === 1) {
            $f = $filters[0];
            $structuredQuery['where'] = $this->buildFieldFilter($f['field'], $f['op'], $f['value']);
        } elseif (count($filters) > 1) {
            $fieldFilters = [];
            foreach ($filters as $f) {
                $fieldFilters[] = $this->buildFieldFilter($f['field'], $f['op'], $f['value']);
            }
            $structuredQuery['where'] = [
                'compositeFilter' => [
                    'op' => 'AND',
                    'filters' => $fieldFilters,
                ]
            ];
        }

        // ORDER BY
        if (!empty($orderBy)) {
            $structuredQuery['orderBy'] = array_map(function ($o) {
                return [
                    'field' => ['fieldPath' => $o['field']],
                    'direction' => strtoupper($o['direction'] ?? 'ASCENDING'),
                ];
            }, $orderBy);
        }

        // LIMIT
        if ($limit !== null) {
            $structuredQuery['limit'] = $limit;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json'
        ])->timeout(30)->post($url, ['structuredQuery' => $structuredQuery]);

        if (!$response->successful()) {
            throw new \Exception('Firestore query failed: ' . $response->body());
        }

        $documents = $response->json();
        $result = [];

        foreach ($documents as $item) {
            if (isset($item['document'])) {
                $converted = $this->convertFirestoreDocument($item['document']);
                if ($converted) {
                    $result[] = $converted;
                }
            }
        }

        return $result;
    }

    private function buildFieldFilter(string $field, string $op, $value): array
    {
        $opMap = [
            '==' => 'EQUAL',
            '!=' => 'NOT_EQUAL',
            '<'  => 'LESS_THAN',
            '<=' => 'LESS_THAN_OR_EQUAL',
            '>'  => 'GREATER_THAN',
            '>=' => 'GREATER_THAN_OR_EQUAL',
            'in' => 'IN',
            'not-in' => 'NOT_IN',
            'array-contains' => 'ARRAY_CONTAINS',
            'array-contains-any' => 'ARRAY_CONTAINS_ANY',
            // Already uppercased operators
            'EQUAL' => 'EQUAL',
            'IN' => 'IN',
            'ARRAY_CONTAINS' => 'ARRAY_CONTAINS',
        ];

        $firestoreOp = $opMap[$op] ?? strtoupper($op);

        // IN and NOT_IN operators expect arrayValue
        if (in_array($firestoreOp, ['IN', 'NOT_IN', 'ARRAY_CONTAINS_ANY'])) {
            $firestoreValue = ['arrayValue' => ['values' => array_map([$this, 'convertToFirestoreValue'], (array)$value)]];
        } else {
            $firestoreValue = $this->convertToFirestoreValue($value);
        }

        return [
            'fieldFilter' => [
                'field' => ['fieldPath' => $field],
                'op' => $firestoreOp,
                'value' => $firestoreValue,
            ]
        ];
    }

    // ─── WRITE OPERATIONS ────────────────────────────────────────

    /**
     * Add a new document with auto-generated ID.
     * Returns ['id' => docId, ...fields].
     */
    public function addDocument(string $collection, array $data): array
    {
        $url = "{$this->baseUrl}/{$collection}";

        $fields = $this->convertPhpToFirestoreFields($data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($url, ['fields' => $fields]);

        if (!$response->successful()) {
            throw new \Exception('Firestore add document failed: ' . $response->body());
        }

        $result = $response->json();
        $pathParts = explode('/', $result['name'] ?? '');
        $docId = end($pathParts);

        return array_merge($data, ['id' => $docId]);
    }

    /**
     * Set (create or overwrite) a document with a specific ID.
     */
    public function setDocument(string $collection, string $documentId, array $data): array
    {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}";

        $fields = $this->convertPhpToFirestoreFields($data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->timeout(30)->patch($url, ['fields' => $fields]);

        if (!$response->successful()) {
            throw new \Exception('Firestore set document failed: ' . $response->body());
        }

        return array_merge($data, ['id' => $documentId]);
    }

    /**
     * Update specific fields of a document (merge).
     * $data is a flat key-value array of fields to update.
     */
    public function updateDocument(string $collection, string $documentId, array $data): array
    {
        $fieldPaths = array_keys($data);
        $maskParams = implode('&', array_map(fn($f) => 'updateMask.fieldPaths=' . urlencode($f), $fieldPaths));
        $url = "{$this->baseUrl}/{$collection}/{$documentId}?{$maskParams}";

        $fields = $this->convertPhpToFirestoreFields($data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->timeout(30)->patch($url, ['fields' => $fields]);

        if (!$response->successful()) {
            throw new \Exception('Firestore update document failed: ' . $response->body());
        }

        return $this->convertFirestoreDocument($response->json()) ?? [];
    }

    /**
     * Delete a document.
     */
    public function deleteDocument(string $collection, string $documentId): bool
    {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->timeout(30)->delete($url);

        if (!$response->successful() && $response->status() !== 404) {
            throw new \Exception('Firestore delete document failed: ' . $response->body());
        }

        return true;
    }

    // ─── HELPER: PHP → FIRESTORE FIELD CONVERSION ───────────────

    private function convertPhpToFirestoreFields(array $data): array
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if ($key === 'id') continue; // Skip the ID field
            $fields[$key] = $this->convertToFirestoreValue($value);
        }
        return $fields;
    }

    private function convertToFirestoreValue($value)
    {
        if (is_null($value)) {
            return ['nullValue' => null];
        } elseif (is_string($value)) {
            return ['stringValue' => $value];
        } elseif (is_int($value)) {
            return ['integerValue' => (string)$value];
        } elseif (is_float($value)) {
            return ['doubleValue' => $value];
        } elseif (is_bool($value)) {
            return ['booleanValue' => $value];
        } elseif ($value instanceof \DateTime || $value instanceof \DateTimeInterface) {
            return ['timestampValue' => $value->format('Y-m-d\TH:i:s.u\Z')];
        } elseif (is_array($value)) {
            // Check if it's an associative array (map) or sequential (array)
            if ($this->isAssociativeArray($value)) {
                $mapFields = [];
                foreach ($value as $k => $v) {
                    $mapFields[$k] = $this->convertToFirestoreValue($v);
                }
                return ['mapValue' => ['fields' => $mapFields]];
            } else {
                if (empty($value)) {
                    return ['arrayValue' => ['values' => []]];
                }
                return ['arrayValue' => ['values' => array_map([$this, 'convertToFirestoreValue'], array_values($value))]];
            }
        }

        return ['stringValue' => (string)$value];
    }

    private function isAssociativeArray(array $arr): bool
    {
        if (empty($arr)) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    // ─── AUTH / TOKEN ────────────────────────────────────────────

    private function getAccessToken()
    {
        if (!$this->accessToken || (time() >= ($this->accessTokenExpiry ?? 0))) {
            $this->accessToken = $this->generateAccessToken();
            $this->accessTokenExpiry = time() + 3500; // refresh slightly before 1hr
        }
        return $this->accessToken;
    }

    private function generateAccessToken()
    {
        $jwt = $this->createJWT();

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    private function createJWT()
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
        $now = time();
        $payload = json_encode([
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);

        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = '';
        openssl_sign($base64Header . '.' . $base64Payload, $signature, $this->serviceAccount['private_key'], 'SHA256');
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }

    // ─── FIRESTORE → PHP CONVERSION ─────────────────────────────

    private function convertFirestoreDocument($firestoreDoc)
    {
        if (!isset($firestoreDoc['fields'])) {
            return null;
        }

        $result = [];
        foreach ($firestoreDoc['fields'] as $key => $value) {
            $result[$key] = $this->convertFirestoreValueToPhp($value);
        }

        // Add document ID if available
        if (isset($firestoreDoc['name'])) {
            $pathParts = explode('/', $firestoreDoc['name']);
            $result['id'] = end($pathParts);
        }

        return $result;
    }

    private function convertFirestoreValueToPhp($value)
    {
        if (isset($value['stringValue'])) {
            return $value['stringValue'];
        } elseif (isset($value['integerValue'])) {
            return (int) $value['integerValue'];
        } elseif (isset($value['doubleValue'])) {
            return (float) $value['doubleValue'];
        } elseif (isset($value['booleanValue'])) {
            return $value['booleanValue'];
        } elseif (isset($value['nullValue'])) {
            return null;
        } elseif (isset($value['timestampValue'])) {
            return $value['timestampValue'];
        } elseif (isset($value['arrayValue']['values'])) {
            return array_map([$this, 'convertFirestoreValueToPhp'], $value['arrayValue']['values']);
        } elseif (isset($value['arrayValue'])) {
            return [];
        } elseif (isset($value['mapValue']['fields'])) {
            $map = [];
            foreach ($value['mapValue']['fields'] as $k => $v) {
                $map[$k] = $this->convertFirestoreValueToPhp($v);
            }
            return $map;
        } elseif (isset($value['mapValue'])) {
            return [];
        }
        return null;
    }
}
