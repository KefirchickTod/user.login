<?php


namespace App;


class DB
{
    private static $instance;
    private $connection;

    private function __construct($dsn = null, $username = null, $password = null, $options = [])
    {
        $dsn = $dsn ?? 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
        $username = $username ?? DB_USER;
        $password = $password ?? DB_PASS;
        $options = $options ?? [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"];

        try {
            $this->connection = new \PDO($dsn, $username, $password, $options);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->exec("SET time_zone = '" . date('P') . "'");
            $this->connection->exec('SET names utf8');
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return DB
     */
    public static function getInstance(): DB
    {
        if (empty(self::$instance)) {
            self::$instance = new DB;
        }

        return self::$instance;
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    /**
     * @param $error
     * @param $sql_string
     */
    public function error($error, $sql_string)
    {
        error_log($error . ' / ' . $sql_string);
    }

    /**
     * @param $sql_string
     * @return array|false|\PDOStatement
     */
    public function querySql($sql_string)
    {
        if (isset($sql_string)) {
            try {
                return $this->connection->query($sql_string);
            } catch (\PDOException $e) {
                $this->error($e->getMessage(), $sql_string);
            }
        } else {
            $this->error('', '');
        }
        return [];
    }

    /**
     * @param $table
     * @param $db_array
     * @param bool $ignore
     * @return bool|string
     */
    public function addSql($table, $db_array, $ignore = false)
    {
        if (isset($db_array, $table) && is_array($db_array)) {
            $db_keys = array_keys($db_array);
            $db_values = array_values($db_array);
            $sql_pre = 'INSERT ' . ($ignore ? 'IGNORE ' : '') . 'INTO ' . $table . ' (' . implode(', ',
                    $db_keys) . ') ';
            $sql_pre .= ' values (' . implode(', ', array_fill(0, count($db_keys), '?')) . ')';
            try {
                $this->connection->beginTransaction();
                $STH = $this->connection->prepare($sql_pre);
                $STH->execute($db_values);
                $lastInsertId = $this->connection->lastInsertId();
                $this->connection->commit();

                return $lastInsertId;
            } catch (\PDOException $e) {

                $this->connection->rollBack();
                $this->error($e->getMessage(), $STH->queryString);
                return $e->getMessage();
            }
        }
        return false;
    }

    /**
     * @param $table
     * @param $db_array
     * @return bool|int|string
     */
    public function insertOrUpdateSql($table, $db_array)
    {
        if (isset($db_array, $table) && is_array($db_array)) {
            $db_keys = array_keys($db_array);
            $db_values = array_values($db_array);
            $db_values = array_merge($db_values, $db_values);
            $sql_pre = 'INSERT INTO ' . $table . ' (' . implode(', ', $db_keys) . ') ';
            $sql_pre .= ' values (' . implode(', ', array_fill(0, count($db_keys), '?')) . ') ';
            $sql_pre .= ' ON DUPLICATE KEY UPDATE ';
            $sql_pre .= implode(' = ? , ', $db_keys) . ' = ? ';
            try {
                $this->connection->beginTransaction();
                $STH = $this->connection->prepare($sql_pre);
                $STH->execute($db_values);
                $lastInsertId = $this->connection->lastInsertId();
                if ($lastInsertId == 0) {
                    $lastInsertId = $STH->rowCount();
                }
                $this->connection->commit();

                return $lastInsertId;
            } catch (\PDOException $e) {
                $this->connection->rollBack();
                $this->error($e->getMessage(), $STH->queryString);
                return false;
            }
        }
        return false;
    }

    /**
     * @param $table
     * @param $db_array
     * @param $where
     * @param bool $returnCount
     * @return bool|int
     */
    public function updateSql($table, $db_array, $where, $returnCount = false)
    {
        if (isset($db_array, $table) && is_array($db_array)) {
            $db_keys = array_keys($db_array);
            $db_values = array_values($db_array);
            $sql_pre = 'UPDATE ' . $table . ' SET ';
            $sql_pre .= implode(' = ? , ', $db_keys) . ' = ? ';
            if ($where) {
                $sql_pre .= ' WHERE ' . $where;
            }


            try {
                $this->connection->beginTransaction();
                $STH = $this->connection->prepare($sql_pre);

                $STH->execute($db_values);
                $this->connection->commit();
                if ($returnCount) {
                    return $STH->rowCount();
                }

                return true;
            } catch (\PDOException $e) {

                $this->connection->rollBack();
                $this->error($e->getMessage(), $STH->queryString);
                error_log($e->getMessage());
                return false;
            }
        }
        return false;
    }

    /**
     * @param $table
     * @param string $select
     * @param string $where
     * @param string $order
     * @param string $group
     * @param string $limit
     * @param string $join
     * @return array
     */
    public function selectSql($table, $select = '*', $where = '', $order = '', $group = '', $limit = '', $join = '')
    {
        $sql_pre = 'SELECT ' . $select . ' FROM ' . $table;
        if ($join) {
            $sql_pre .= ' ' . $join . ' ';
        }
        if ($where) {
            $sql_pre .= ' WHERE ' . $where;
        }
        if ($group) {
            $sql_pre .= ' GROUP BY ' . $group;
        }
        if ($order) {
            $sql_pre .= ' ORDER BY ' . $order;
        }
        if ($limit) {
            $sql_pre .= ' LIMIT ' . $limit;
        }
        // var_dump($sql_pre);exit;
        try {
            $STH = $this->connection->prepare($sql_pre);
            $STH->execute();

            return $STH->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->error($e->getMessage() . $sql_pre, $STH->queryString);
        }
        return [];
    }

    /**
     * @param $table
     * @param $db_array
     * @return bool|int
     */
    public function deleteSql($table, $db_array)
    {
        if (isset($db_array, $table) && is_array($db_array)) {
            $db_keys = array_keys($db_array);
            $db_values = array_values($db_array);
            $sql_pre = 'DELETE FROM ' . $table . ' WHERE ';
            $sql_pre .= implode('=? AND ', $db_keys) . '=? ';
            try {
                $this->connection->beginTransaction();
                $STH = $this->connection->prepare($sql_pre);
                $STH->execute($db_values);
                $this->connection->commit();

                return $STH->rowCount();
            } catch (\PDOException $e) {
                $this->connection->rollBack();
                $this->error($e->getMessage(), $STH->queryString);

                return false;
            }
        }
        return false;
    }

    /**
     * @param $table_name
     * @param $db_array
     * @return bool
     */
    public function dynamicInsert($table_name, $db_array): bool
    {
        try {
            $list = $this->connection->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$table_name}'")->fetchAll(\PDO::FETCH_ASSOC);
            $list = array_flip(array_map(function ($value) {
                return $value['COLUMN_NAME'];
            }, $list));
            $data = [];
            foreach ($db_array as $name => $value) {
                if (isset($list[$name])) {
                    $data[$name] = $value;
                }
            }
            return $this->addSql($table_name, $data);
        } catch (\PDOException $exception) {
            $this->connection->rollBack();
            $this->error($exception->getMessage(), '');
            error_log($exception->getMessage());
            return false;
        }
    }
}