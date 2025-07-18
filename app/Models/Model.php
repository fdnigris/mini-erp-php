<?php

namespace App\Models;

use App\Services\Database;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function create(array $data) {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$values})");
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }

    public function update($id, array $data) {
        try {
            error_log("Tentando atualizar registro na tabela {$this->table}");
            error_log("ID: " . $id);
            error_log("Dados: " . print_r($data, true));

            $sets = [];
            foreach (array_keys($data) as $key) {
                $sets[] = "`{$key}` = ?";
            }
            $setsStr = implode(', ', $sets);
            
            $sql = "UPDATE `{$this->table}` SET {$setsStr} WHERE `{$this->primaryKey}` = ?";
            error_log("SQL: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $values = array_merge(array_values($data), [$id]);
            error_log("Valores: " . print_r($values, true));
            
            $result = $stmt->execute($values);
            error_log("Resultado da execução: " . ($result ? "sucesso" : "falha"));
            error_log("Linhas afetadas: " . $stmt->rowCount());
            
            return $result && $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Erro ao atualizar: " . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
} 