<?php
class Role extends Model {

    public function getAll(): array {
        return $this->db->table('roles')->get();
    }

    public function getById(int $id): ?array {
        $results = $this->db->table('roles')->where('id', $id)->get();
        return $results[0] ?? null;
    }
}
