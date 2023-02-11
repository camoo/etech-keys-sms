<?php

declare(strict_types=1);

namespace Etech\Sms\Database;

use Etech\Sms\Interfaces\Drivers;
use mysqli;
use mysqli_result;

/**
 * Class MySQL
 */
class MySQL implements Drivers
{
    private $table_prefix = '';

    private $dbh_connect = null;

    private mixed $dbh_query = null;

    private $dbh_error = null;

    private $dbh_escape = null;

    private ?mysqli $connection = null;

    private static $_ahConfigs = [];

    public static function getInstance(array $options = []): self
    {
        static::$_ahConfigs = $options;

        return new self();
    }

    public function getDB(): ?self
    {
        [$this->dbh_connect, $this->dbh_query, $this->dbh_error, $this->dbh_escape] = $this->getMysqlHandlers();
        if ($this->connection = $this->db_connect($this->getConf())) {
            return $this;
        }

        return null;
    }

    public function escape_string(string $string): string
    {
        return call_user_func($this->dbh_escape, $this->connection, trim($string));
    }

    public function close(): bool
    {
        return mysqli_close($this->connection);
    }

    public function query(string $query): mysqli_result|bool
    {
        $result = call_user_func($this->dbh_query, $this->connection, $query);

        if (!$result) {
            echo $this->getError();
        }

        return $result;
    }

    public function insert(string $table, array $variables = []): bool
    {
        //Make sure the array isn't empty
        if (empty($variables)) {
            return false;
        }

        $sql = 'INSERT INTO ' . $table;
        $fields = [];
        $values = [];
        foreach ($variables as $field => $value) {
            $fields[] = $field;
            $values[] = "'" . $this->escape_string($value) . "'";
        }
        $fields = ' (' . implode(', ', $fields) . ')';
        $values = '(' . implode(', ', $values) . ')';

        $sql .= $fields . ' VALUES ' . $values;
        $query = $this->query($sql);

        if (!$query) {
            return false;
        }

        return true;
    }

    protected function db_connect(array $config)
    {
        if (isset($config['table_prefix'])) {
            $this->table_prefix = $config['table_prefix'];
        }

        try {
            $connection = call_user_func(
                $this->dbh_connect,
                $config['db_host'],
                $config['db_user'],
                $config['db_password'],
                $config['db_name'],
                $config['db_port']
            );
        } catch (\Exception $err) {
            echo 'Failed to connect to MySQL: ' . $err->getMessage() . "\n";

            return 0;
        }

        return $connection;
    }

    protected function getError()
    {
        return mysqli_error($this->connection);
    }

    private function getConf()
    {
        $default = ['table_prefix' => '', 'db_host' => 'localhost', 'db_port' => 3306];
        static::$_ahConfigs += $default;

        return static::$_ahConfigs;
    }

    private function getMysqlHandlers()
    {
        return ['mysqli_connect', 'mysqli_query', 'mysqli_error', 'mysqli_real_escape_string'];
    }
}
