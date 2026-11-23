<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $model = app(config('videotutorial.models.videotutorial.class'));

        $model->getConnection()->getSchemaBuilder()->table($model->getTable(), function (Blueprint $table): void {
            $table->string('context_uploader_id')->nullable();
        });
    }

    public function down(): void
    {
        $model = app(config('videotutorial.models.videotutorial.class'));

        $model->getConnection()->getSchemaBuilder()->table($model->getTable(), function (Blueprint $table): void {
            $table->dropColumn('context_uploader_id');
        });
    }
};
