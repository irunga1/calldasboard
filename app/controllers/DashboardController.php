<?php
class DashboardController extends Controller {

    public function index() {
        $this->view('dashboard', [
            'pageTitle' => 'Dashboard — CallDashboard',
            'username'  => $_SESSION['username'] ?? 'Usuario',
            'role_id'   => $_SESSION['role_id']  ?? 2,
        ]);
    }

    public function calls() {
        $this->view('calls', [
            'pageTitle' => 'Detalle de llamadas — CallDashboard',
            'username'  => $_SESSION['username'] ?? 'Usuario',
            'role_id'   => $_SESSION['role_id']  ?? 2,
        ]);
    }
}
