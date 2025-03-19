<?php

namespace Schema;

use App\Database\Schema\Schema as SchemaSchema;
use App\Database\Schema\SchemaMethods;
use DateTimeImmutable;

return new class extends SchemaSchema
{
    protected string $table = 'offer';

    /**
     * Чтобы закатить миграцию
     *
     * @return void
     */
    public function up(): void
    {
        static::create($this->table, function (SchemaMethods $table) {
            $table->id();
            $table->string('name');
            $table->text('link');
            $table->string('title');
            $table->text('description');
            $table->boolean('is_active', true);
            $table->string('btn_color')->default('#007BFF');
            $table->string('btn_text')->default('Акция');
            $table->datetime('created_at')->default((new DateTimeImmutable())->format('Y-m-d H:i:s'));
            $table->datetime('updated_at')->default((new DateTimeImmutable())->format('Y-m-d H:i:s'));
            $table->datetime('deleted_at')->nullable();
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
