<?php

/** ========================================
 *
 *
 *! Файл: AbstractModel.php
 ** Директория: app\database\model\AbstractModel.php
 *? Цель: Слой Models Layer для создание общих методов для удобной работы с БД
 * Создано: 2025-03-08 23:54:54
 *
 *
============================================ */

namespace App\Database\Model;

use App\Database\Adapter;
use Common\Helpers\Functions;

abstract class AbstractModel extends Adapter
{
    public string $table;

    public function __construct()
    {
        $this->table = Functions::toSnakeCase((new \ReflectionClass($this))->getShortName());
    }

    /**
     * Для получение условии запроса используя ids
     *
     * @param array<array-key, int> $ids
     * @return array<array-key, array<string, string>>
     */
    private static function getConditionsByIds(array $ids): array
    {
        $condition = [];
        foreach ($ids as $value) {
            $condition[] = ['id', '&', $value];
        }
        /**
         * @var array<array-key, array<string, string>>
         */
        return $condition;
    }







    /**
     * Для вставки данных в таблицу
     *
     * @param array<string, mixed> $data ['name' => 'Имя', 'age' => 35]
     * @param boolean $getId true/false нужен чтобы после создание вернул id
     * @return array
     */
    public function create(array $data, bool $getId = false): array
    {
        return static::insert($this->table, $data, $getId);
    }

    /**
     * Массовая вставка данных в таблицу
     *
     * @param array<int, array<string, mixed>> $data
     * @return array
     */
    public function bulkInsert(array $data): array
    {
        return $this->insertMultiple($this->table, $data);
    }








    /**
     * Для обновление данных таблицы
     *
     * @param int $id 1,2,3,4,5...
     * @param array $data ['name' => 'Новое имя', 'age' => 35]
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function modify(int $id, array $data, bool $isStrong = true): array
    {
        return static::update($this->table, $data, [['id', '=', "$id"]], static::getStrong($isStrong));
    }

    /**
     * Обновление нескольких столбцов
     *
     * @param array $data ['name' => 'Новое имя', 'age' => 35]
     * @param array<int> $ids [1,2,3,4,5...]
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function refactor(array $data, array $ids, bool $isStrong = true): array
    {
        $condition = static::getConditionsByIds($ids);
        return static::update($this->table, $data, $condition, static::getStrong($isStrong));
    }

    /**
     * Обновление таблицы с разными условиями
     *
     * @param array $data ['name' => 'Новое имя', 'age' => 35]
     * @param array<array-key, array<string, string>> $conditions [['id', '=', 1], ['id', '&', 1], ['id', '<', 1]]
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function updateBy(array $data, array $conditions, bool $isStrong = true): array
    {
        return static::update($this->table, $data, $conditions, static::getStrong($isStrong));
    }






    /**
     * Для удаление записи по id
     *
     * @param integer $id 1,2,3,4,5...
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function remove(int $id, bool $isStrong = true): array
    {
        return static::delete($this->table, [["id", "=", "$id"]], static::getStrong($isStrong));
    }

    /**
     * Удаление нескольких записей с помощью id
     *
     * @param array<int> $ids [1,2,3,4,5...]
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function purge(array $ids, bool $isStrong = true): array
    {
        $condition = static::getConditionsByIds($ids);

        return static::delete($this->table, $condition, static::getStrong($isStrong));
    }

    /**
     * Удаление по условии
     *
     * @param array<array-key, array<array-key, string>> $conditions [['id', '=', 1], ['id', '&', 1], ['id', '<', 1]]
     * @param boolean $isStrong
     * @return array
     */
    public function removeBy(array $conditions, bool $isStrong = true): array
    {
        return static::delete($this->table, $conditions, static::getStrong($isStrong));
    }





    /**
     * Проверить на существование записи по id
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        $element = $this->select($this->table, ['id'])->where([['id', '=', $id]])->limit(1)->get();
        return !empty($element['data'][0]['id']) && $element['data'][0]['id'] == $id;
    }

    /**
     * Для получение данных по id
     *
     * @param integer $id 1,2,3,4,5...
     * @param array<string> $fields ['name', 'id', 'age', 'created_at']
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function findById(int $id, array $fields = ['*'], bool $isStrong = true): array
    {
        return $this->select($this->table, $fields)->where([["id", "=", "$id"]], static::getStrong($isStrong))->limit(1)->get();
    }

    /**
     * Для получение первого результата используя условие
     *
     * @param array<array-key, array<string>> $conditions [['id', '=', 1], ['id', '&', 1], ['id', '<', 1]]
     * @param array<string> $fields ['name', 'id', 'age', 'created_at']
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function findOne(array $conditions, array $fields, bool $isStrong = true): array
    {
        return $this->select($this->table, $fields)->where($conditions, static::getStrong($isStrong))->limit(1)->get();
    }

    /**
     * Для получение данных по ids
     *
     * @param ?array<int> $ids
     * @param array<string> $fields
     * @param boolean $isStrong
     * @return array
     */
    public function findByIds(?array $ids = null, ?array $fields = ['*'], ?int $limit = null, bool $isStrong = true): array
    {
        if ($ids) {
            $conditions = static::getConditionsByIds($ids);
            return $this->select($this->table, $fields)->where($conditions, static::getStrong($isStrong))->get();
        }
        if ($limit) {
            return $this->select($this->table, $fields)->limit($limit)->get();
        }
        return $this->select($this->table, $fields)->get();
    }

    /**
     * Для получение результатов используя условие и лимит
     *
     * @param array<array-key, array<string, string>> $conditions [['id', '=', 1], ['id', '&', 1], ['id', '<', 1]]
     * @param array<string> $fields ['name', 'id', 'age', 'created_at']
     * @param integer $limit 1,2,3,4,5...
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function find(array $conditions, array $fields = ['*'], int $limit = 1, bool $isStrong = true): array
    {
        return $this->select($this->table, $fields)->where($conditions, static::getStrong($isStrong))->limit($limit)->get();
    }

    /**
     * Для получение всех данных с таблицы без лимита используя условие
     *
     * @param ?array<array-key, array<string, string>> $conditions [['id', '=', 1], ['id', '&', 1], ['id', '<', 1]]
     * @param array<string> $fields ['name', 'id', 'age', 'created_at']
     * @param boolean $isStrong true/false AND/OR
     * @return array
     */
    public function all(?array $conditions = null, array $fields = ['*'], bool $isStrong = true): array
    {
        if ($conditions) {
            return $this->select($this->table, $fields)->where($conditions, static::getStrong($isStrong))->get();
        }
        return $this->select($this->table, $fields)->get();
    }

    /**
     * Получить количество записей в таблице
     *
     * @return array
     */
    public function count(): array
    {
        return $this->GetCount($this->table)->get();
    }

    /**
     * Получить количество записей в таблице с условиями
     *
     * @param array<array-key, array<string, string>> $conditions [['id', '=', 1], ['id', '&', 1], ['id', '<', 1]]
     * @param bool $isStrong true/false AND/OR
     * @return array
     */
    public function countBy(array $conditions, bool $isStrong = true): array
    {
        return $this->GetCount($this->table)->where($conditions, static::getStrong($isStrong))->get();
    }

    /**
     * One-to-One связь (hasOne)
     *
     * @param class-string $relatedModel Связанный класс модели (например, App\Models\Profile)
     * @param string $foreignKey Внешний ключ в связанной таблице (например, user_id)
     * @param int|string $id по которому будут находить связь
     * @return array|null
     */
    public function hasOne(string $relatedModel, string $foreignKey, int|string $id): ?array
    {
        /** @psalm-suppress MixedMethodCall */
        $relatedInstance = new $relatedModel();
        if (!$relatedInstance instanceof self) {
            throw new \InvalidArgumentException("{$relatedModel} должен быть экземпляром " . self::class);
        }

        $relatedTable = $relatedInstance->table;

        return $this->select($relatedTable)
            ->where([[$foreignKey, '=', (string)$id]])
            ->limit(1)
            ->get();
    }

    /**
     * One-to-Many связь (hasMany)
     *
     * @param class-string $relatedModel Связанный класс модели
     * @param string $foreignKey Внешний ключ в связанной таблице
     * @param int|string $id ID текущей модели, по которому искать связь
     * @return array
     */
    public function hasMany(string $relatedModel, string $foreignKey, int|string $id): array
    {
        /** @psalm-suppress MixedMethodCall */
        $relatedInstance = new $relatedModel();
        if (!$relatedInstance instanceof self) {
            throw new \InvalidArgumentException("{$relatedModel} должен быть экземпляром " . self::class);
        }

        $relatedTable = $relatedInstance->table;

        return $this->select($relatedTable)
            ->where([[$foreignKey, '=', (string) $id]])
            ->get();
    }

    /**
     * Many-to-Many связь (belongsToMany)
     *
     * @param class-string $relatedModel Связанный класс модели
     * @param string $pivotTable Промежуточная таблица (например, user_roles)
     * @param string $foreignPivotKey Внешний ключ текущей модели в pivot таблице
     * @param string $relatedPivotKey Внешний ключ связанной модели в pivot таблице
     * @param int|string $id ID текущей модели, по которому искать связь
     * @param string $relatedKey Ключ в связанной таблице (обычно id)
     * @return array
     */
    public function belongsToMany(
        string $relatedModel,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        int|string $id,
        string $relatedKey = 'id'
    ): array {
        /** @psalm-suppress MixedMethodCall */
        $relatedInstance = new $relatedModel();
        if (!$relatedInstance instanceof self) {
            throw new \InvalidArgumentException("{$relatedModel} должен быть экземпляром " . self::class);
        }

        $relatedTable = $relatedInstance->table;

        return $this->select("{$relatedTable}.*")
            ->join($pivotTable, "{$pivotTable}.{$relatedPivotKey}", "=", "{$relatedTable}.{$relatedKey}")
            ->where([["{$pivotTable}.{$foreignPivotKey}", '=', (string) $id]])
            ->get();
    }
}
