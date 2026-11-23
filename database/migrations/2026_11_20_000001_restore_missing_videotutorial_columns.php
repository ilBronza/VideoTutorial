<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $model = app(config('videotutorial.models.videotutorial.class'));
        $schema = $model->getConnection()->getSchemaBuilder();
        $tableName = $model->getTable();
        $columns = array_map('strtolower', $schema->getColumnListing($tableName));

        $schema->table($tableName, static function (Blueprint $table) use ($columns): void {
            if (!in_array('file', $columns, true)) {
                $table->string('file')->nullable();
            }

            if (!in_array('active', $columns, true)) {
                $table->boolean('active')->nullable();
            }

            if (!in_array('show', $columns, true)) {
                $table->boolean('show')->nullable();
            }
        });
    }

    public function down(): void
    {
        // These columns belong to the original schema. Keep them and their
        // values whether they were created originally or restored here.
    }
};
