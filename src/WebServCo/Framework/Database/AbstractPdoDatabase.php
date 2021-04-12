<?php

declare(strict_types=1);

namespace WebServCo\Framework\Database;

use WebServCo\Framework\Environment\Config;
use WebServCo\Framework\Exceptions\DatabaseException;

abstract class AbstractPdoDatabase extends \WebServCo\Framework\AbstractLibrary
{
    use \WebServCo\Framework\Traits\DatabaseTrait;
    use \WebServCo\Framework\Traits\DatabaseAddQueryTrait;

    protected \PDO $db;
    protected \PDOStatement $stmt;

    abstract protected function getDataSourceName(string $host, int $port, string $dbname): string;

    /**
    * @param array<string,string|array<mixed>> $settings
    */
    public function __construct(array $settings = [])
    {
        parent::__construct($settings);

        try {
            $dsn = $this->getDataSourceName(
                Config::string('APP_DBMS_HOST'),
                Config::int('APP_DBMS_PORT'),
                Config::string('APP_DBMS_DBNAME'),
            );
            $this->db = new \PDO(
                $dsn,
                Config::string('APP_DBMS_USERNAME'),
                Config::string('APP_DBMS_PASSWD'),
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_PERSISTENT => false,
                ],
            );
        } catch (\Throwable $e) { // PDOException/RuntimeException/Exception
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    public function affectedRows(): int
    {
        if (!($this->stmt instanceof \PDOStatement)) {
            throw new DatabaseException('No Statement object available.');
        }
        return $this->stmt->rowCount();
    }

    public function escape(string $string): string
    {
        return $this->db->quote($string);
    }

    /**
    * @param array<int,float|int|string> $params
    * @return bool|int|string|null
    */
    public function getColumn(string $query, array $params = [], int $columnNumber = 0)
    {
        $this->query($query, $params);
        return $this->stmt->fetchColumn($columnNumber);
    }

    /**
    * @param array<int,float|int|string> $params
    * @return array<string,float|int|string>
    */
    public function getRow(string $query, array $params = []): array
    {
        $this->query($query, $params);
        $result = $this->stmt->fetch(\PDO::FETCH_ASSOC);
        return $this->handleStatementReturn($result);
    }

    /**
    * @param array<int,float|int|string> $params
    * @return array<int,array<string,float|int|string>>
    */
    public function getRows(string $query, array $params = []): array
    {
        $this->query($query, $params);
        $result = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->handleStatementReturn($result);
    }

    /**
    * Get last inserted Id.
    *
    * https://dev.mysql.com/doc/refman/5.5/en/information-functions.html#function_last-insert-id
    * If you insert multiple rows using a single INSERT statement,
    * LAST_INSERT_ID() returns the value generated for the first inserted row only.
    * The reason for this is to make it possible to reproduce easily the same
    * INSERT statement against some other server.
    *
    * PDO:
    * Returns the ID of the last inserted row, or the last value from a sequence object,
    * depending on the underlying driver.
    * For example, PDO_PGSQL requires you to specify the name of a sequence object for the name parameter.
    */
    public function lastInsertId(string $name = ''): int
    {
        return (int) $this->db->lastInsertId($name);
    }

    public function numRows(): int
    {
        if (!($this->stmt instanceof \PDOStatement)) {
            throw new DatabaseException('No Statement object available.');
        }
        if ('mysql' !== $this->setting('driver', '')) {
            throw new DatabaseException('Not implemented.');
        }
        return $this->stmt->rowCount();
    }

    /**
    * @param array<int,float|int|string|null> $params
    */
    public function query(string $query, array $params = []): \PDOStatement
    {
        if (empty($query)) {
            throw new DatabaseException('No query specified.');
        }

        try {
            if (!empty($params)) {
                $this->stmt = $this->db->prepare($query);
                $this->bindParams($params);
                $this->stmt->execute();
            } else {
                $stmt = $this->db->query($query);
                if (!($stmt instanceof \PDOStatement)) {
                    throw new DatabaseException('Error executing query');
                }
                $this->stmt = $stmt;
            }
            return $this->stmt;
        } catch (\Throwable $e) { // \PDOException, \RuntimeException
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    /**
    * @param mixed $value
    */
    public function setAttribute(int $attribute, $value): bool
    {
        return $this->db->setAttribute($attribute, $value);
    }

    /**
    * @param array<int,array<int,mixed>> $queries
    */
    public function transaction(array $queries): bool
    {
        try {
            $this->db->beginTransaction();
            foreach ($queries as $item) {
                if (!isset($item[0])) {
                    throw new DatabaseException('No query specified.');
                }
                $params = $item[1] ?? [];
                $this->query($item[0], $params);
            }
            $this->db->commit();
            return true;
        } catch (\Throwable $e) { // DatabaseException, \PDOException, \RuntimeException
            $this->db->rollBack();
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    /**
    * @param array<mixed> $data
    */
    protected function bindParams(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        if (!\is_array($data)) {
            throw new DatabaseException('"Parameters" is not an array.');
        }

        $i = 1;
        foreach ($data as $item) {
            if (\is_array($item)) {
                foreach ($item as $v) {
                    $this->validateParam($v);
                    $this->stmt->bindValue($i, $v, $this->getDataType((string) $v));
                    $i++;
                }
            } else {
                $this->validateParam($item);
                $this->stmt->bindValue($i, $item, $this->getDataType((string) $item));
                $i++;
            }
        }
        return true;
    }

    protected function getDataType(string $variable): int
    {
        $type = \gettype($variable);

        switch ($type) {
            case 'NULL':
                return \PDO::PARAM_NULL;
            case 'integer':
                return \PDO::PARAM_INT;
            case 'boolean':
            // causes data not to be inserted
            //return \PDO::PARAM_BOOL;
            case 'string':
            case 'double':
            case 'array':
            case 'object':
            case 'resource':
            case 'resource (closed)':
            case 'unknown type':
            default:
                return \PDO::PARAM_STR;
        }
    }

    /**
    * @param mixed $param
    */
    protected function validateParam($param): bool
    {
        if (\is_array($param)) {
            throw new DatabaseException('Parameter is an array.');
        }
        return true;
    }

    /**
    * Make sure PDO Statement returns an array when there are no errors.
    *
    * "In all cases, false is returned on failure."
    * However, false is also returned when there are no results.
    *
    * @param bool|array<mixed> $result
    * @return array<mixed>
    */
    protected function handleStatementReturn($result): array
    {
        if (\is_array($result)) {
            // All is ok.
            return $result;
        }
        $errorInfo = $this->stmt->errorInfo();
        // 0 = "SQLSTATE"
        // 1 = "Driver specific error code"
        // 2 = "Driver specific error message"
        if ('00000' === $errorInfo[0]) {
            // "Successful completion", so no results
            return [];
        }
        throw new DatabaseException($errorInfo[2]);
    }
}
