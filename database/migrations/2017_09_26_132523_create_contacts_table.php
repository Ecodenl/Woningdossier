<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamps();
            $table->string('number')->default('');
            $table->string('type_id', '16')->nullable()->default(null);
            $table->string('full_name', 255)->default('');
            $table->string('status_id', 16)->nullable()->default(null);
            $table->date('member_since')->nullable()->default(null);
            $table->date('member_until')->nullable()->default(null);
            $table->boolean('newsletter')->default(false);
            $table->text('iban');
            $table->boolean('liable')->default(false);
            $table->float('liability_amount')->default(0);

            $table->integer('owner_id')->unsigned()->nullable()->default(null);
            $table->foreign('owner_id')->references('id')->on('users') ->onDelete('restrict');

            $table->unsignedInteger('created_by_id')->nullable()->default(null);
            $table->unsignedInteger('updated_by_id')->nullable()->default(null);
            $table->foreign('created_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict');
            $table->foreign('updated_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
