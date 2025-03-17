<?php

/** ========================================
 *
 *
 *! Файл: Adapter.php
 ** Директория: app\database\Adapter.php
 *? Цель: Для работы с sql запросами
 *? Описание: Самый нижний слои работы с БД данные класс работает с sql напрямую
 * Создано: 2025-03-08 18:47:27
 *
 *
============================================ */

namespace App\Database;

use App\Helpers\Logs\Log;
use InvalidArgumentException;
use PDO;
use PDOException;
use Throwable;

abstract class Adapter
{
    /**
     * @var PDO|null
     */
    protected static PDO|null $pdo;

    protected string $query = "";

    protected array $data = [];

    protected string $primaryKey = 'id';

    /**
     * Для выявление жесткости
     *
     * @param boolean $strong
     * @return string
     */
    protected static function getStrong(bool $strong): string
    {
        return $strong ? "AND" : "OR";
    }

    /**
     * Для подключение
     *
     * @return void
     */
    protected static function connect(): void
    {
        if (!isset(self::$pdo)) {
            self::$pdo = (new Connect())->getPdo();
        }
    }

    /**
     * Получение значение по типу :name, :login, :password
     *
     * @param array<string> $data
     * @return string
     */
    protected static function getValues(array $data): string
    {
        return implode(', ', array_map(fn($f) => ":$f", $data));
    }

    /**
     * Получение полей по типу name, login, password
     *
     * @param array<string> $data
     * @return string
     */
    protected static function getFields(array $data): string
    {
        return implode(', ', $data);
    }

    /**
     * Получение полей и значение по типу name = :name, login = :login, password = :password
     *
     * @param array $data
     * @return string
     */
    protected static function generateFieldsAndValue(array $data): string
    {
        return implode(', ', array_map(fn($f) => "$f = :$f", $data));
    }

    /**
     * Получение полей и значение по типу name = :name, login = :login, password = :password используя ключи
     *
     * @param array $data
     * @return string
     */
    protected static function generateFieldsAndValueWithArrayKey(array $data): string
    {
        return implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
    }

    /**
     * Для задавание уникальных условии
     *
     * @param array<array-key, array<array-key, string>> $conditions [['id', '=', 1], ['id', '&', 1], ['id', '>', 1]]
     * @param string $separator AND|OR
     * @return array{
     *      conditions: string,
     *      params: array
     * } ['conditions' => "SQL условие", 'params' => "Параметры к SQL условии"]
     */
    public static function getUniversalCondition(array $conditions, string $separator): array
    {
        $whereClauses = [];
        $params = [];
        $groupedValues = [];

        foreach ($conditions as $index => $condition) {
            if (count($condition) !== 3) {
                throw new InvalidArgumentException("Each condition must be an array of 3 elements: field, operator, value.");
            }

            [$field, $operator, $value] = $condition;

            if ($operator === '&') {
                $groupedValues[$field][] = $value;
                continue;
            }

            $paramKey = "{$field}_{$index}";

            $whereClauses[] = "$field $operator :$paramKey";
            $params[$paramKey] = $value;
        }

        foreach ($groupedValues as $field => $values) {
            $inKeys = [];
            foreach ($values as $index => $value) {
                $paramKey = "{$field}_in_{$index}";
                $inKeys[] = ":$paramKey";
                $params[$paramKey] = $value;
            }

            $whereClauses[] = "$field IN (" . implode(', ', $inKeys) . ")";
        }

        return [
            'conditions' => !empty($whereClauses) ? implode(" {$separator} ", $whereClauses) : '1=1',
            'params' => $params,
        ];
    }














    /**
     * Для написание sql запросов
     *
     * @param string $query
     * @param array $params
     * @return array array<string, string|array<array-key, array<string, mixed>>>
     */
    protected static function sql(string $query, array $params = []): array
    {
        try {
            self::connect();

            if (self::$pdo) {
                $stmt = self::$pdo->prepare($query);
                $result = $stmt->execute($params);

                $isSelectQuery = str_starts_with(trim(strtolower($query)), 'select');
                $isInsertQuery = str_starts_with(trim(strtolower($query)), 'insert');

                return [
                    'code' => '200',
                    'message' => 'Success',
                    'data' => $isSelectQuery ? $stmt->fetchAll(PDO::FETCH_ASSOC) : ($isInsertQuery ? self::$pdo->lastInsertId() : $result),
                ];
            }
            return [
                'code' => '500',
                'message' => 'Ошибка при подключение к БД!'
            ];
        } catch (PDOException $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Для написание DDL запросов по типу CREATE TABLE, TRUNCATE TABLE
     *
     * @param string $query
     * @return array
     */
    protected static function ddl(string $query): array
    {
        try {
            self::connect();

            if (self::$pdo) {
                $result = self::$pdo->exec($query);

                return [
                    'code' => '200',
                    'message' => 'DDL query executed successfully.',
                    'data' => [
                        'affected_rows' => $result,
                    ],
                ];
            }
            return [
                'code' => '500',
                'message' => 'Ошибка при подключение к БД!'
            ];
        } catch (PDOException $e) {
            Log::database("DDL query error: " . $e->getMessage());

            return [
                'code' => $e->getCode(),
                'message' => 'DDL query execution failed.',
                'data' => null,
            ];
        } catch (Throwable $e) {
            Log::database("Unexpected error: " . $e->getMessage());

            return [
                'code' => 500,
                'message' => 'An unexpected error occurred.',
                'data' => null,
            ];
        }
    }













    /**
     * Метод для добавление записей
     *
     * @param string $table
     * @param array<string, mixed> $data
     * @param boolean $getId
     * @return array
     */
    public static function insert(string $table, array $data, bool $getId = false): array
    {
        try {
            static::connect();
            $fields = static::getFields(array_keys($data));
            $values = static::getValues(array_keys($data));

            $sql = "INSERT INTO $table ($fields) VALUES ($values)";
            if ($getId) {
                $sql .= " RETURNING id";
            }

            return static::sql($sql, $data);
        } catch (PDOException $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }













    /**
     * Метод для выбора полей.
     *
     * @param string $table Таблица
     * @param array<array-key, string> $selectedFields Поля для выборки
     * @return $this
     */
    public function select(string $table, array $selectedFields = ['*']): self
    {
        $this->query = "SELECT " . $this->getFields($selectedFields) . " FROM " . $table;
        return $this;
    }

    /**
     * Метод для добавления условий фильтрации.
     *
     * @param array<array-key, array<array-key, string>> $conditions Условия в формате [['поле', 'оператор', 'значение'], ...]
     * @param string $separator Разделитель условий (по умолчанию "AND")
     * @return $this
     */
    public function where(array $conditions, string $separator = "AND"): self
    {
        $data = $this->getUniversalCondition($conditions, $separator);

        $this->query .= " WHERE " . $data['conditions'];
        $this->data = $data['params'];

        return $this;
    }

    /**
     * Метод для выполнения JOIN между таблицами.
     *
     * @param string $joinTable Таблица для JOIN
     * @param string $firstKey Поле из первой таблицы
     * @param string $operator Оператор сравнения (по умолчанию "=")
     * @param string $secondKey Поле из второй таблицы
     * @param string $joinType Тип соединения (INNER, LEFT, RIGHT, FULL)
     * @return $this
     */
    public function join(string $joinTable, string $firstKey, string $operator, string $secondKey, string $joinType = "INNER"): self
    {
        $this->query .= " {$joinType} JOIN {$joinTable} ON {$firstKey} {$operator} {$secondKey}";
        return $this;
    }

    /**
     * LEFT JOIN - выбирает все данные из левой таблицы и совпадающие из правой
     *
     * @param string $joinTable
     * @param string $firstKey
     * @param string $operator
     * @param string $secondKey
     * @return self
     */
    public function leftJoin(string $joinTable, string $firstKey, string $operator, string $secondKey): self
    {
        return $this->join($joinTable, $firstKey, $operator, $secondKey, "LEFT");
    }

    /**
     * RIGHT JOIN — выбирает все данные из правой таблицы и совпадающие из левой
     *
     * @param string $joinTable
     * @param string $firstKey
     * @param string $operator
     * @param string $secondKey
     * @return self
     */
    public function rightJoin(string $joinTable, string $firstKey, string $operator, string $secondKey): self
    {
        return $this->join($joinTable, $firstKey, $operator, $secondKey, "RIGHT");
    }

    /**
     * FULL JOIN — выбирает все данные из обеих таблиц (если поддерживается БД)
     *
     * @param string $joinTable
     * @param string $firstKey
     * @param string $operator
     * @param string $secondKey
     * @return self
     */
    public function fullJoin(string $joinTable, string $firstKey, string $operator, string $secondKey): self
    {
        return $this->join($joinTable, $firstKey, $operator, $secondKey, "FULL OUTER");
    }

    /**
     * CROSS JOIN — перемножает все строки из двух таблиц (без условий)
     *
     * @param string $joinTable
     * @return self
     */
    public function crossJoin(string $joinTable): self
    {
        $this->query .= " CROSS JOIN {$joinTable}";
        return $this;
    }

    /**
     * Метод для ограничения количества записей.
     *
     * @param int $limit Количество записей
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->query .= " LIMIT " . $limit;
        return $this;
    }

    /**
     * Метод для выполнения запроса.
     *
     * @return array
     * @throws PDOException
     */
    public function get(): array
    {
        try {
            $this->connect();

            $result = $this->sql($this->query, $this->data);

            $this->query = "";
            $this->data = [];

            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Query execution failed: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Для выбора несколько данных из таблицы
     *
     * @param string $table
     * @param array<array-key, array<array-key, string>> $conditions
     * @param array<array-key, string> $fields
     * @param string $separator
     * @return static
     */
    public function search(string $table, array $conditions, array $fields = ['*'], string $separator = "OR"): self
    {
        $data = static::getUniversalCondition($conditions, $separator);

        $this->query = "SELECT " . static::getFields($fields) . " FROM " . $table . " WHERE " . $data['conditions'];
        $this->data = $data['params'];

        return $this;
    }

    /**
     * Для проверки существование данных в таблице
     *
     * @param string $table
     * @param string $key
     * @param string $value
     * @return array
     */
    protected static function exists(string $table, string $key, string $value): array
    {
        try {
            static::connect();
            /**
             * @var array<array-key, array>
             */
            $result = self::sql("SELECT COUNT(*) as count FROM {$table} WHERE {$key} = :value", ['value' => $value]);
            $state = !empty($result) && $result[0]['count'] > 0;
            return [
                'code' => '200',
                'message' => 'Данные успешно получены',
                'data' => $state
            ];
        } catch (PDOException $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Для получение количества записей в таблице
     *
     * @param string $table
     * @return self
     */
    public function getCount(string $table): self
    {
        $this->query = "SELECT COUNT(*) FROM " . $table;
        return $this;
    }



















    /**
     * Для удаление данных
     *
     * @param string $table
     * @param array<array-key, array<array-key, string>> $condition
     * @param string $separator
     * @return array
     */
    public static function delete(string $table, array $condition, string $separator = "AND"): array
    {
        try {
            $query = static::getUniversalCondition($condition, $separator);

            $sql = "DELETE FROM " . $table . " WHERE " . $query['conditions'];

            return static::sql($sql, $query['params']);
        } catch (PDOException $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Для сбрасывание таблицы(очистка и сбрасывание например auto_increment)
     *
     * @param string $table
     * @return array
     */
    protected static function reset(string $table): array
    {
        try {
            static::connect();
            return static::ddl("TRUNCATE TABLE " . $table);
        } catch (PDOException $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Для очистки таблицы
     *
     * @param string $table
     * @return array
     */
    protected static function clear(string $table): array
    {
        try {
            static::connect();
            return static::ddl("DELETE FROM " . $table);
        } catch (PDOException $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }















    /**
     * Метод для обновление таблицы
     *
     * @param string $table
     * @param array $data
     * @param array<array-key, array<array-key, string>> $condition
     * @param string $separator
     * @return array
     */
    public static function update(string $table, array $data, array $condition, string $separator = "AND"): array
    {
        try {
            $query = static::getUniversalCondition($condition, $separator);

            $sql = "UPDATE " . $table . " SET " . static::generateFieldsAndValueWithArrayKey($data) . " WHERE " . $query['conditions'];

            $params = array_merge($query['params'], $data);

            return static::sql($sql, $params);
        } catch (PDOException $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }











    /**
     * Извлекает данные из JSON-поля в таблице
     *
     * @param string $table Название таблицы
     * @param string $jsonField JSON-поле
     * @param string $key Ключ внутри JSON
     * @param array<string, mixed> $conditions Условия выборки
     * @return array<int, mixed>
     */
    public static function jsonExtract(string $table, string $jsonField, string $key, array $conditions = []): array
    {
        $cond = static::getUniversalCondition($conditions, "AND");
        $sql = "SELECT JSON_UNQUOTE(JSON_EXTRACT(`{$jsonField}`, '$.\"{$key}\"')) AS value 
            FROM `{$table}` WHERE {$cond['conditions']}";

        return static::sql($sql, $cond['params']);
    }

    /**
     * Увеличивает значение числового столбца
     *
     * @param string $table Название таблицы
     * @param string $field Название столбца
     * @param int $amount Величина увеличения
     * @param array<string, mixed> $conditions Условия обновления
     * @return array<int, mixed>
     */
    public static function increment(string $table, string $field, int $amount, array $conditions): array
    {
        $cond = static::getUniversalCondition($conditions, "AND");
        $sql = "UPDATE `{$table}` SET `{$field}` = `{$field}` + :amount WHERE {$cond['conditions']}";

        return static::sql($sql, array_merge($cond['params'], ['amount' => $amount]));
    }

    /**
     * Чтобы уменьшить значение столбца
     *
     * @param string $table
     * @param string $field
     * @param integer $amount
     * @param array $conditions
     * @return array
     */
    public static function decrement(string $table, string $field, int $amount, array $conditions): array
    {
        return static::increment($table, $field, -$amount, $conditions);
    }

    /**
     * Получение случайной записи с таблицы
     *
     * @param string $table
     * @param string $fields
     * @param integer $limit
     * @return array
     */
    public static function random(string $table, string $fields = '*', int $limit = 1): array
    {
        $sql = "SELECT $fields FROM $table ORDER BY RAND() LIMIT :limit";

        return static::sql($sql, ['limit' => $limit]);
    }
}
