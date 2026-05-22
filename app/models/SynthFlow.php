<?php
class SynthFlow {

    private string $apiKey;
    private string $endpoint;
    private string $model;

    public function __construct() {
        $this->apiKey   = SYNTHFLOW_API_KEY;
        $this->endpoint = SYNTHFLOW_ENDPOINT;
        $this->model    = SYNTHFLOW_MODEL;
    }

    public function getCalls(int $page = 1, int $limit = 20): array {
        $url = $this->endpoint . '?' . http_build_query([
            'model_id' => $this->model,
            'page'     => $page,
            'limit'    => $limit,
        ]);
        return $this->request('GET', $url);
    }

    public function makeCall(string $phone, string $agentName = ''): array {
        $payload = [
            'phone_number' => $phone,
            'model'        => $this->model,
            'agent_name'   => $agentName,
        ];
        return $this->request('POST', $this->endpoint, $payload);
    }

    private function request(string $method, string $url, array $body = []): array {
        if (!function_exists('curl_init')) {
            return ['error' => 'cURL no está disponible en este servidor.'];
        }

        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST,       true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['error' => 'Error de conexión: ' . $curlErr];
        }

        $data = json_decode($response, true);

        if ($httpCode >= 400) {
            return [
                'error' => ($data['message'] ?? $data['error'] ?? "HTTP $httpCode"),
                'code'  => $httpCode,
            ];
        }

        return $data ?? [];
    }
}
