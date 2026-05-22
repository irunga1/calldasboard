<?php
class QueryBuilder {
    protected $pdo;
    protected $table;
    protected $select = '*';
    protected $where  = [];
    protected $limit;
    protected $order;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function table($table) {
        $this->table  = $table;
        $this->where  = [];
        $this->limit  = null;
        $this->order  = null;
        $this->select = '*';
        return $this;
    }

    public function select($fields = '*') {
        $this->select = $fields;
        return $this;
    }

    public function where($field, $value) {
        $this->where[] = [$field, $value];
        return $this;
    }

    public function orderBy($field, $direction = 'ASC') {
        $this->order = "$field $direction";
        return $this;
    }

    public function limit($limit) {
        $this->limit = (int)$limit;
        return $this;
    }

    public function get() {
        $sql = "SELECT {$this->select} FROM `{$this->table}`";

        if (!empty($this->where)) {
            $conditions = array_map(fn($w) => "`{$w[0]}` = :{$w[0]}", $this->where);
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        if ($this->order) $sql .= " ORDER BY {$this->order}";
        if ($this->limit) $sql .= " LIMIT {$this->limit}";

        $stmt = $this->pdo->prepare($sql);
        foreach ($this->where as $w) {
            $stmt->bindValue(":{$w[0]}", $w[1]);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}
