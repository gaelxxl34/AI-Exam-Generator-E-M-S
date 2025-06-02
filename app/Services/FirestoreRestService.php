<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirestoreRestService
{
    private $projectId;
    private $accessToken;
    private $serviceAccount;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->loadServiceAccount();
    }

    private function loadServiceAccount()
    {
        if (env('FIREBASE_CREDENTIALS_BASE64')) {
            $this->serviceAccount = json_decode(base64_decode(env('FIREBASE_CREDENTIALS_BASE64')), true);
        } else {
            $this->serviceAccount = json_decode(file_get_contents(env('FIREBASE_CREDENTIALS')), true);
        }
    }

    public function getDocument($collection, $documentId)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";
        
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

    public function getCollection($collection, $filters = [])
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";
        
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

    public function queryCollection($collection, $field, $operator, $value)
    {
        // For more complex queries, we can use the REST API's runQuery endpoint
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
        
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => $collection]],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => $field],
                        'op' => strtoupper($operator),
                        'value' => $this->convertToFirestoreValue($value)
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json'
        ])->timeout(30)->post($url, $query);

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

    private function convertToFirestoreValue($value)
    {
        if (is_string($value)) {
            return ['stringValue' => $value];
        } elseif (is_int($value)) {
            return ['integerValue' => (string)$value];
        } elseif (is_bool($value)) {
            return ['booleanValue' => $value];
        } elseif (is_array($value)) {
            return ['arrayValue' => ['values' => array_map([$this, 'convertToFirestoreValue'], $value)]];
        }
        
        return ['stringValue' => (string)$value];
    }

    private function getAccessToken()
    {
        if (!$this->accessToken) {
            $this->accessToken = $this->generateAccessToken();
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

    private function convertFirestoreDocument($firestoreDoc)
    {
        if (!isset($firestoreDoc['fields'])) {
            return null;
        }
        
        $result = [];
        foreach ($firestoreDoc['fields'] as $key => $value) {
            if (isset($value['stringValue'])) {
                $result[$key] = $value['stringValue'];
            } elseif (isset($value['arrayValue']['values'])) {
                $result[$key] = array_map(function($item) {
                    return $item['stringValue'] ?? $item;
                }, $value['arrayValue']['values']);
            } elseif (isset($value['booleanValue'])) {
                $result[$key] = $value['booleanValue'];
            } elseif (isset($value['integerValue'])) {
                $result[$key] = (int) $value['integerValue'];
            } elseif (isset($value['doubleValue'])) {
                $result[$key] = (float) $value['doubleValue'];
            } elseif (isset($value['timestampValue'])) {
                $result[$key] = $value['timestampValue'];
            }
        }
        
        // Add document ID if available
        if (isset($firestoreDoc['name'])) {
            $pathParts = explode('/', $firestoreDoc['name']);
            $result['id'] = end($pathParts);
        }
        
        return $result;
    }
}
