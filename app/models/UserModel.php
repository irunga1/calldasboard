<?php
class UserModel extends Model {
    public function getUsers() {
        return $this->db->table('usuario')->get();
    }

    public function getUserById($id) {
        return $this->db->table('usuari')->where('id', $id)->get();
    }
    public function runCustomQuery() {
        return $this->db->query("SELECT COUNT(*) as total FROM users WHERE active = ?", [1]);
    }

}
