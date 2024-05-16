<?php namespace Pensoft\Cropper\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateCroppersTable Migration
 */
class CreateCroppersTable extends Migration
{
    public function up()
    {
        Schema::create('pensoft_cropper_croppers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type', 191)->nullable();
            $table->string('path', 191)->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pensoft_cropper_croppers');
    }
}
