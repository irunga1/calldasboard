<?php
class Controller {
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = BASE_PATH . "/app/views/$view.php";
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "<h3>Vista '$view' no encontrada.</h3>";
            return;
        }
        require $viewFile;
    }

    protected function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
