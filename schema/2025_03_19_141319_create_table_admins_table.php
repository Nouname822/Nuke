<?php

namespace Schema;

use App\Database\Schema\Schema as SchemaSchema;
use App\Database\Schema\SchemaMethods;
use DateTimeImmutable;

return new class extends SchemaSchema
{
    protected string $table = 'admins';

    /**
     * Чтобы закатить миграцию
     *
     * @return void
     */
    public function up(): void
    {
        static::create($this->table, function (SchemaMethods $table) {
            $table->id();
            $table->string('login')->unique('login');
            $table->string('email')->unique('email');
            $table->string('avatar')->default('/assets/img/profile.svg');
            $table->string('name')->default('Иван Иванов');
            $table->string('password');
            $table->integer('failed_login')->default('0');
            $table->enum('status', ['active', 'inactive']);
            $table->enum('role', ['super_admin', 'admin', 'moderator']);
            $table->datetime('last_login')->default((new DateTimeImmutable())->format('Y-m-d H:i:s'));
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
