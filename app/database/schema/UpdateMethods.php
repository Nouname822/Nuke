<?php

namespace App\Database\Schema;

class UpdateMethods
{
    private string $query = '';

    private array $types = [
        'string'   => 'VARCHAR(255)',
        'text'     => 'TEXT',
        'int'  => 'INTEGER',
        'boolean'  => 'BOOLEAN',
        'json'     => 'JSONB',
        'float'    => 'REAL',
        'datetime' => 'TIMESTAMP',
    ];

    /**
     * @param string $type
     * @param integer|null $length
     * @param boolean $notNull
     * @param mixed $default
     * @return string
     */
    private function columnDefinition(string $type, ?int $length = null, bool $notNull = false, mixed $default = null): string
    {
        /**
         * @var string
         */
        $sql = $this->types[$type] ?? strtoupper($type);
        if (isset($length) && str_contains($sql, '(') === false) {
            $sql .= "($length)";
        }

        if ($notNull) {
            $sql .= " NOT NULL";
        }

        if (!is_null($default)) {
            /**
             * @var string
             */
            $defaultValue = is_string($default) ? "'$default'" : $default;
            $sql .= " DEFAULT $defaultValue";
        }

        return $sql;
    }

    public function addColumn(string $name, string $type, ?int $length = null, bool $notNull = false, mixed $default = null): self
    {
        $this->query .= "ADD COLUMN $name " . $this->columnDefinition($type, $length, $notNull, $default) . ", ";
        return $this;
    }

    public function dropColumn(string $name): self
    {
        $this->query .= "DROP COLUMN $name, ";
        return $this;
    }

    public function modifyColumn(string $name, string $type, ?int $length = null, bool $notNull = false, mixed $default = null): self
    {
        $this->query .= "ALTER COLUMN $name SET DATA TYPE " . $this->columnDefinition($type, $length, $notNull, $default) . ", ";
        return $this;
    }

    public function addIndex(string $column, string $indexName = ''): self
    {
        $indexName = $indexName ?: "idx_$column";
        $this->query .= "ADD INDEX $indexName ($column), ";
        return $this;
    }

    public function dropIndex(string $indexName): self
    {
        $this->query .= "DROP INDEX $indexName, ";
        return $this;
    }

    public function addForeignKey(string $column, string $referenceTable, string $referenceColumn, string $onDelete = 'CASCADE', string $onUpdate = 'CASCADE'): self
    {
        $fkName = "fk_{$column}_{$referenceTable}";
        $this->query .= "ADD CONSTRAINT $fkName FOREIGN KEY ($column) REFERENCES $referenceTable ($referenceColumn) ON DELETE $onDelete ON UPDATE $onUpdate, ";
        return $this;
    }

    public function dropForeignKey(string $fkName): self
    {
        $this->query .= "DROP FOREIGN KEY $fkName, ";
        return $this;
    }

    public function renameColumn(string $oldName, string $newName): self
    {
        $this->query .= "RENAME COLUMN $oldName TO $newName, ";
        return $this;
    }

    public function nullable(string $column): self
    {
        $this->query .= "ALTER COLUMN $column DROP NOT NULL, ";
        return $this;
    }

    /**
     * @param string $name
     * @param array<string> $values
     * @param boolean $notNull
     * @param mixed $default
     * @return self
     */
    public function enum(string $name, array $values, bool $notNull = false, mixed $default = null): self
    {
        $valuesList = "'" . implode("', '", $values) . "'";
        $this->query .= "ADD COLUMN $name ENUM($valuesList)" . ($notNull ? " NOT NULL" : "") . (!is_null($default) ? " DEFAULT '$default'" : "") . ", ";
        return $this;
    }

    /**
     * @param string $name
     * @param array<string> $values
     * @param boolean $notNull
     * @param mixed $default
     * @return self
     */
    public function set(string $name, array $values, bool $notNull = false, mixed $default = null): self
    {
        $valuesList = "'" . implode("', '", $values) . "'";
        $this->query .= "ADD COLUMN $name SET($valuesList)" . ($notNull ? " NOT NULL" : "") . (!is_null($default) ? " DEFAULT '$default'" : "") . ", ";
        return $this;
    }

    public function timestamp(string $name, bool $notNull = false, mixed $default = null): self
    {
        return $this->addColumn($name, 'timestamp', null, $notNull, $default);
    }

    public function softDeletes(): self
    {
        return $this->timestamp('deleted_at', false, null);
    }

    public function dropSoftDeletes(): self
    {
        return $this->dropColumn('deleted_at');
    }

    public function timestamps(): self
    {
        return $this->timestamp('created_at', true, 'CURRENT_TIMESTAMP')
            ->timestamp('updated_at', true, 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    public function dropTimestamps(): self
    {
        return $this->dropColumn('created_at')->dropColumn('updated_at');
    }

    public function unique(string $column): self
    {
        $this->query .= "ADD UNIQUE ($column), ";
        return $this;
    }

    public function dropUnique(string $column): self
    {
        $this->query .= "DROP INDEX $column, ";
        return $this;
    }

    public function primaryKey(string $column): self
    {
        $this->query .= "ADD PRIMARY KEY ($column), ";
        return $this;
    }

    public function dropPrimaryKey(): self
    {
        $this->query .= "DROP PRIMARY KEY, ";
        return $this;
    }

    public function defaultCharset(string $charset): self
    {
        $this->query .= "DEFAULT CHARACTER SET $charset, ";
        return $this;
    }

    public function engine(string $engine): self
    {
        $this->query .= "ENGINE = $engine, ";
        return $this;
    }

    public function build(string $table): string
    {
        return "ALTER TABLE $table " . rtrim($this->query, ', ') . ";";
    }

    // Методы для типов данных
    public function string(string $name, int $length = 255, bool $notNull = false, mixed $default = null): self
    {
        return $this->addColumn($name, 'string', $length, $notNull, $default);
    }

    public function text(string $name, bool $notNull = false, mixed $default = null): self
    {
        return $this->addColumn($name, 'text', null, $notNull, $default);
    }

    public function integer(string $name, bool $notNull = false, mixed $default = null): self
    {
        return $this->addColumn($name, 'integer', null, $notNull, $default);
    }

    public function boolean(string $name, bool $notNull = false, mixed $default = null): self
    {
        return $this->addColumn($name, 'boolean', null, $notNull, $default);
    }

    public function json(string $name, bool $notNull = false, mixed $default = null): self
    {
        return $this->addColumn($name, 'json', null, $notNull, $default);
    }

    public function float(string $name, bool $notNull = false, mixed $default = null): self
    {
        return $this->addColumn($name, 'float', null, $notNull, $default);
    }

    public function datetime(string $name, bool $notNull = false, mixed $default = null): self
    {
        return $this->addColumn($name, 'datetime', null, $notNull, $default);
    }
}
