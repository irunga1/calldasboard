<?php
class ApiController extends Controller {

    public function calls() {
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate   = trim($_GET['end_date']   ?? '');
        $page      = max(1, (int)($_GET['page'] ?? 1));

        // SynthFlow acepta máximo 100; con filtro de fechas traemos todos los disponibles
        $limit = ($startDate || $endDate) ? 100 : 20;

        $synthflow = new SynthFlow();
        $raw = $synthflow->getCalls($page, $limit);

        if (isset($raw['error'])) {
            $this->json(['results' => [], 'error' => $raw['error']]);
            return;
        }

        $calls      = $raw['response']['calls'] ?? $raw['calls'] ?? $raw['data'] ?? [];
        $pagination = $raw['response']['pagination'] ?? [];

        // start_time viene en milisegundos desde epoch
        if ($startDate !== '') {
            $startMs = (int)(strtotime($startDate . ' 00:00:00') * 1000);
            $calls   = array_values(array_filter($calls, fn($c) => (int)($c['start_time'] ?? 0) >= $startMs));
        }

        if ($endDate !== '') {
            $endMs = (int)(strtotime($endDate . ' 23:59:59') * 1000);
            $calls = array_values(array_filter($calls, fn($c) => (int)($c['start_time'] ?? PHP_INT_MAX) <= $endMs));
        }

        $this->json([
            'results'    => $calls,
            'total'      => $pagination['total_records'] ?? count($calls),
            'filtered'   => count($calls),
            'page'       => $page,
        ]);
    }
}
