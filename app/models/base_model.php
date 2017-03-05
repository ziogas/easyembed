<?php

class base_model
{
    private $pdo,
            $table;

    public function __construct()
    {
        $this->pdo = db_pdo::load('main');
    }

    public function set_table($table)
    {
        $this->table = $table;
    }

    private function get_from_fields(&$fields)
    {
        $search = array();

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                // Like ['time'=>array('>', 123)];
                if (sizeof($value) == 2) {
                    $search[] = $key.$value[0].'?';
                    $fields[$key] = $value[1];
                // Like [array('time', '>', 123)];
                } elseif (sizeof($value) == 3) {
                    $search[] = $value[0].$value[1].'?';
                    $fields[$key] = $value[2];
                }
            } else {
                $search [] = $key .'=?';
            }
        }

        return $search;
    }

    public function sql($query, $params = array())
    {
        $res = $this->pdo->prepare($query);
        $res->execute($params);
        return $res;
    }

    public function insert($fields)
    {
        if (!$this->table) {
            return false;
        }

        $query = 'INSERT INTO `'.$this->table.'` ('. implode(', ', array_keys($fields)) .') VALUES ('. substr(str_repeat('?,', sizeof($fields)), 0, -1) .')';
        $this->sql($query, array_values($fields));

        return (int)$this->pdo->lastInsertId();
    }

    public function update($fields, $id)
    {
        if (!$this->table) {
            return false;
        }

        $insert = array();

        foreach ($fields as $key => $value) {
            $insert []= $key .'=?';
        }

        $fields []= $id;
        $query = 'UPDATE `'.$this->table.'` SET '. implode(', ', $insert) .' WHERE id=?';

        $this->sql($query, array_values($fields));

        return (int)$id;
    }

    public function exists($fields, $selection = 'id')
    {
        if (!$this->table) {
            return false;
        }

        $search = $this->get_from_fields($fields);

        $query = 'SELECT '.$selection.' FROM `'.$this->table.'` WHERE '. implode(' AND ', $search) .' LIMIT 1';
        $res = $this -> sql($query, array_values($fields));

        $result = $res->fetch(PDO::FETCH_ASSOC);

        return (isset($result[$selection]) ? true : false);
    }

    public function get($fields = array(), $selection = '*')
    {
        if (!$this->table) {
            return false;
        }

        if (sizeof($fields)) {
            $search = $this->get_from_fields($fields);

            $query = 'SELECT '.$selection.' FROM `'.$this->table.'` WHERE '. implode(' AND ', $search) .' LIMIT 1';
        } else {
            $query = 'SELECT '.$selection.' FROM `'.$this->table.'` LIMIT 1';
        }

        $res = $this->sql($query, array_values($fields));

        return $res->fetch(PDO::FETCH_ASSOC);
    }

    public function search($fields = array(), $selection = '*', $limit = null)
    {
        if (!$this->table) {
            return false;
        }

        if (sizeof($fields)) {
            $search = $this->get_from_fields($fields);

            $query = 'SELECT '.$selection.' FROM `'.$this->table.'` WHERE '. implode(' AND ', $search);
        } else {
            $query = 'SELECT '.$selection.' FROM `'.$this->table.'`';
        }

        if ($limit) {
            $query .= ' LIMIT '.$limit;
        }

        $res = $this->sql($query, array_values($fields));

        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count($fields = array())
    {
        if (!$this->table) {
            return false;
        }

        return $this->get($fields, 'COUNT(*) AS `count`');
    }

    public function delete($fields, $limit = null)
    {
        if (!$this->table) {
            return false;
        }

        $search = $this->get_from_fields($fields);

        $query = 'DELETE FROM `'.$this->table.'` WHERE '. implode(' AND ', $search);

        if ($limit) {
            $query .= ' LIMIT '.$limit;
        }

        return $this->sql($query, array_values($fields));
    }
}
