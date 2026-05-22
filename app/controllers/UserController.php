<?php
class UserController extends Controller {

    private function requireAdmin(): void {
        if (($_SESSION['role_id'] ?? 2) != 1) {
            header('Location: ' . route('dashboard'));
            exit;
        }
    }

    private function flash(string $type, string $msg): void {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    public function index(): void {
        $this->requireAdmin();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->view('users', [
            'pageTitle' => 'Usuarios — CallDashboard',
            'username'  => $_SESSION['username'] ?? 'Usuario',
            'role_id'   => $_SESSION['role_id']  ?? 2,
            'users'     => (new User())->getAll(),
            'roles'     => (new Role())->getAll(),
            'flash'     => $flash,
        ]);
    }

    public function store(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->index(); return; }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId   = (int)($_POST['role_id'] ?? 2);

        if ($username === '' || $password === '') {
            $this->flash('error', 'El nombre de usuario y la contraseña son obligatorios.');
        } elseif (strlen($password) < 6) {
            $this->flash('error', 'La contraseña debe tener al menos 6 caracteres.');
        } else {
            $model = new User();
            if ($model->usernameExists($username)) {
                $this->flash('error', 'El usuario "' . htmlspecialchars($username) . '" ya existe.');
            } else {
                $model->create($username, $password, $roleId);
                $this->flash('success', 'Usuario "' . htmlspecialchars($username) . '" creado correctamente.');
            }
        }

        header('Location: ' . route('user'));
        exit;
    }

    public function update(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->index(); return; }

        $id       = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $roleId   = (int)($_POST['role_id'] ?? 2);
        $password = $_POST['password'] ?? '';

        if ($id === 0 || $username === '') {
            $this->flash('error', 'Datos inválidos.');
            header('Location: ' . route('user')); exit;
        }

        $model = new User();

        if ($model->usernameExists($username, $id)) {
            $this->flash('error', 'El usuario "' . htmlspecialchars($username) . '" ya existe.');
            header('Location: ' . route('user')); exit;
        }

        if ($password !== '' && strlen($password) < 6) {
            $this->flash('error', 'La contraseña debe tener al menos 6 caracteres.');
            header('Location: ' . route('user')); exit;
        }

        $model->update($id, $username, $roleId, $password !== '' ? $password : null);

        if ($id === (int)($_SESSION['user_id'] ?? 0)) {
            $_SESSION['username'] = $username;
        }

        $this->flash('success', 'Usuario actualizado correctamente.');
        header('Location: ' . route('user'));
        exit;
    }

    public function destroy(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->index(); return; }

        $id = (int)($_POST['id'] ?? 0);

        if ($id === (int)($_SESSION['user_id'] ?? 0)) {
            $this->flash('error', 'No puedes eliminar tu propia cuenta.');
        } elseif ($id > 0) {
            (new User())->delete($id);
            $this->flash('success', 'Usuario eliminado.');
        }

        header('Location: ' . route('user'));
        exit;
    }
}
