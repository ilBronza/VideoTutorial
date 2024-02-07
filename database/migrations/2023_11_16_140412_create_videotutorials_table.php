<?php

use IlBronza\VideoTutorial\Models\Videotutorial;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideotutorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Videotutorial::make()->getTable(), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('title')->nullable();
            $table->string('file')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sorting_index')->nullable();

            $table->string('link')->nullable();
            $table->text('embed')->nullable();

            $table->boolean('active')->nullable();
            $table->boolean('show')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table(Videotutorial::make()->getTable(), function (Blueprint $table) {
            $table->uuid('parent_id')->after('name')->nullable();
            $table->foreign('parent_id')->references('id')->on(Videotutorial::make()->getTable());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Videotutorial::make()->getTable());
    }
}
