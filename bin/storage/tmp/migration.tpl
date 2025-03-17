<?php

namespace Schema;

use App\Database\Schema\Schema as SchemaSchema;
use App\Database\Schema\SchemaMethods;

return new class extends SchemaSchema
{
    protected string $table = '{{ table }}';

    /**
     * Чтобы закатить миграцию
     *
     * @return void
     */
    public function up(): void
    {
        static::create($this->table, function (SchemaMethods $table) {
            $table->id();
            $table->string('login');
            $table->string('password');
        });
    }

    /**
     * Чтобы откатить миграцию
     *
     * @return void
     */
    public function down(): void
    {
        static::drop($this->table);
    }
};
