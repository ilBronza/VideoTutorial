<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $this->table(static function (Blueprint $table): void {
            $table->json('route_names')->nullable();
        });
    }

    public function down(): void
    {
        $this->table(static function (Blueprint $table): void {
            $table->dropColumn('route_names');
        });
    }

    private function table(Closure $callback): void
    {
        $model = app(config('videotutorial.models.videotutorial.class'));

        $model->getConnection()->getSchemaBuilder()->table($model->getTable(), $callback);
    }
};
