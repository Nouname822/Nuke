<?php

namespace App\Database\Schema;

class SchemaMethods
{
    private string $query = '';

    public function id(): self
    {
        $this->query .= "id BIGSERIAL PRIMARY KEY, ";
        return $this;
    }

    public function bigIncrements(string $column): self
    {
        $this->query .= "$column BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY, ";
        return $this;
    }

    public function increments(string $column): self
    {
        $this->query .= "$column INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY, ";
        return $this;
    }

    public function uuid(string $column): self
    {
        $this->query .= "$column UUID PRIMARY KEY, ";
        return $this;
    }

    public function string(string $column, int $length = 255): self
    {
        $this->query .= "$column VARCHAR($length) NOT NULL, ";
        return $this;
    }

    public function text(string $column): self
    {
        $this->query .= "$column TEXT NOT NULL, ";
        return $this;
    }

    public function integer(string $column, bool $unsigned = false): self
    {
        $type = $unsigned ? "INTEGER CHECK ($column >= 0)" : "INTEGER";
        $this->query .= "$column $type NOT NULL, ";
        return $this;
    }

    public function boolean(string $column, bool $default = false): self
    {
        $defaultValue = $default ? 'TRUE' : 'FALSE';
        $this->query .= "$column BOOLEAN NOT NULL DEFAULT $defaultValue, ";
        return $this;
    }

    public function decimal(string $column, int $precision = 10, int $scale = 2): self
    {
        $this->query .= "$column DECIMAL($precision, $scale) NOT NULL, ";
        return $this;
    }

    public function float(string $column, int $precision = 8, int $scale = 2): self
    {
        $this->query .= "$column FLOAT($precision, $scale) NOT NULL, ";
        return $this;
    }

    public function double(string $column, int $precision = 16, int $scale = 8): self
    {
        $this->query .= "$column DOUBLE PRECISION NOT NULL, ";
        return $this;
    }

    public function json(string $column): self
    {
        $this->query .= "$column JSONB DEFAULT NULL, ";
        return $this;
    }

    /**
     * @param string $column
     * @param array<string> $values
     * @return self
     */
    public function enum(string $column, array $values): self
    {
        $valuesList = implode("', '", $values);
        $this->query .= "$column VARCHAR(255) CHECK ($column IN ('$valuesList')) NOT NULL, ";
        return $this;
    }

    /**
     * @param string $column
     * @param array<string> $values
     * @return self
     */
    public function set(string $column, array $values): self
    {
        $this->query .= "$column TEXT CHECK ($column ~ '^((" . implode("|", $values) . ")(,(" . implode("|", $values) . "))*)?$') NOT NULL, ";
        return $this;
    }

    public function date(string $column): self
    {
        $this->query .= "$column DATE NOT NULL, ";
        return $this;
    }

    public function datetime(string $column): self
    {
        $this->query .= "$column TIMESTAMP NOT NULL, ";
        return $this;
    }

    public function time(string $column): self
    {
        $this->query .= "$column TIME NOT NULL, ";
        return $this;
    }

    public function timestamp(string $column): self
    {
        $this->query .= "$column TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ";
        return $this;
    }

    public function timestamps(): self
    {
        $this->query .= "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ";
        return $this;
    }

    public function nullable(): self
    {
        $this->query = preg_replace('/NOT NULL/', 'NULL', $this->query);
        return $this;
    }

    public function default(string $value): self
    {
        $this->query = preg_replace('/, $/', " DEFAULT '$value', ", $this->query);
        return $this;
    }

    public function unique(string $column): self
    {
        $this->query .= "UNIQUE ($column), ";
        return $this;
    }

    public function foreign(string $column, string $references, string $onTable): self
    {
        $this->query .= "FOREIGN KEY ($column) REFERENCES $onTable($references) ON DELETE CASCADE ON UPDATE CASCADE, ";
        return $this;
    }

    public function renameTable(string $newName): self
    {
        $this->query .= "RENAME TO `$newName`, ";
        return $this;
    }

    public function after(string $column, string $afterColumn): self
    {
        $this->query .= "MODIFY `$column` AFTER `$afterColumn`, ";
        return $this;
    }

    public function first(string $column): self
    {
        $this->query .= "MODIFY `$column` FIRST, ";
        return $this;
    }

    public function comment(string $column, string $comment): self
    {
        $this->query .= "MODIFY `$column` COMMENT '$comment', ";
        return $this;
    }

    public function dropForeignKey(string $keyName): self
    {
        $this->query .= "DROP FOREIGN KEY `$keyName`, ";
        return $this;
    }

    public function dropIndex(string $indexName): self
    {
        $this->query .= "DROP INDEX `$indexName`, ";
        return $this;
    }

    public function changeColumn(string $oldName, string $newName, string $type, bool $notNull = false, mixed $default = null): self
    {
        $this->query .= "CHANGE `$oldName` `$newName` $type" . ($notNull ? " NOT NULL" : "") . (!is_null($default) ? " DEFAULT '$default'" : "") . ", ";
        return $this;
    }

    public function renameColumn(string $oldName, string $newName): self
    {
        $this->query .= "RENAME COLUMN `$oldName` TO `$newName`, ";
        return $this;
    }

    public function setDefault(string $column, mixed $default): self
    {
        $this->query .= "ALTER COLUMN `$column` SET DEFAULT '$default', ";
        return $this;
    }

    public function dropDefault(string $column): self
    {
        $this->query .= "ALTER COLUMN `$column` DROP DEFAULT, ";
        return $this;
    }

    public function dropPrimary(): self
    {
        $this->query .= "DROP PRIMARY KEY, ";
        return $this;
    }

    public function renameIndex(string $oldName, string $newName): self
    {
        $this->query .= "ALTER INDEX `$oldName` RENAME TO `$newName`, ";
        return $this;
    }

    public function enableConstraints(): self
    {
        $this->query .= "SET FOREIGN_KEY_CHECKS = 1, ";
        return $this;
    }

    public function disableConstraints(): self
    {
        $this->query .= "SET FOREIGN_KEY_CHECKS = 0, ";
        return $this;
    }

    public function changeType(string $column, string $newType): self
    {
        $this->query .= "MODIFY COLUMN `$column` $newType, ";
        return $this;
    }

    public function setNullable(string $column, bool $nullable = true): self
    {
        $null = $nullable ? "NULL" : "NOT NULL";
        $this->query .= "MODIFY COLUMN `$column` SET $null, ";
        return $this;
    }

    public function addForeignKey(string $column, string $referencedTable, string $referencedColumn, string $onDelete = 'CASCADE', string $onUpdate = 'CASCADE'): self
    {
        $this->query .= "ADD CONSTRAINT `fk_{$column}_{$referencedTable}` FOREIGN KEY (`$column`) REFERENCES `$referencedTable` (`$referencedColumn`) ON DELETE $onDelete ON UPDATE $onUpdate, ";
        return $this;
    }

    public function build(string $table): string
    {
        return "CREATE TABLE IF NOT EXISTS $table (" . rtrim($this->query, ', ') . ");";
    }
}
