<?php
class User extends Model {

    public function findByUsername(string $username): ?array {
        $r = $this->db->table('users')->where('username', $username)->limit(1)->get();
        return $r[0] ?? null;
    }

    public function findById(int $id): ?array {
        $r = $this->db->table('users')->where('id', $id)->limit(1)->get();
        return $r[0] ?? null;
    }

    public function getAll(): array {
        return $this->db->query(
            'SELECT u.id, u.username, u.role_id, r.role_name, u.created_at
             FROM users u
             JOIN roles r ON r.id = u.role_id
             ORDER BY u.created_at DESC'
        );
    }

    public function usernameExists(string $username, int $excludeId = 0): bool {
        $r = $this->db->query(
            'SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1',
            [$username, $excludeId]
        );
        return !empty($r);
    }

    public function create(string $username, string $password, int $roleId = 1): int {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $this->db->execute(
            'INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, ?)',
            [$username, $hash, $roleId]
        );
    }

    public function update(int $id, string $username, int $roleId, ?string $password = null): bool {
        if ($password !== null) {
            $affected = $this->db->execute(
                'UPDATE users SET username = ?, role_id = ?, password_hash = ? WHERE id = ?',
                [$username, $roleId, password_hash($password, PASSWORD_DEFAULT), $id]
            );
        } else {
            $affected = $this->db->execute(
                'UPDATE users SET username = ?, role_id = ? WHERE id = ?',
                [$username, $roleId, $id]
            );
        }
        return $affected > 0;
    }

    public function delete(int $id): bool {
        return $this->db->execute('DELETE FROM users WHERE id = ?', [$id]) > 0;
    }
}
