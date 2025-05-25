<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('community_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('community_id');
            $table->enum('role', ['admin', 'organizer', 'member']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('community_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('location')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->enum('status', ['pending', 'attending', 'not_attending'])->default('pending');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rsvps');
        Schema::dropIfExists('events');
        Schema::dropIfExists('community_user');
        Schema::dropIfExists('communities');
    }
};