<?php
class AuthController extends Controller {

    public function index() {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . route('dashboard'));
            exit;
        }
        $this->view('login', ['error' => null]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        $utilery  = new Utilery();
        $username = $utilery->sanitize(trim($_POST['username'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->view('login', ['error' => 'Por favor completa todos los campos.']);
            return;
        }

        $userModel = new User();
        $user      = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id']  = $user['role_id'];
            header('Location: ' . route('dashboard'));
            exit;
        }

        $this->view('login', ['error' => 'Usuario o contraseña incorrectos.']);
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . route('auth', 'login'));
        exit;
    }
}
