<?php

namespace Schema;

use App\Database\Schema\Schema as SchemaSchema;
use App\Database\Schema\SchemaMethods;
use DateTimeImmutable;

return new class extends SchemaSchema
{
    protected string $table = 'wheel_items';

    /**
     * Чтобы закатить миграцию
     *
     * @return void
     */
    public function up(): void
    {
        static::create($this->table, function (SchemaMethods $table) {
            $table->id();
            $table->string('prize');
            $table->string('color');
            $table->integer('wheel_id');
            $table->datetime('created_at')->default((new DateTimeImmutable())->format('Y-m-d H:i:s'));
            $table->datetime('updated_at')->default((new DateTimeImmutable())->format('Y-m-d H:i:s'));
            $table->datetime('deleted_at')->nullable();
            $table->foreign('wheel_id', 'id', 'wheel');
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
