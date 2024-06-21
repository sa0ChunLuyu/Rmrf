<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('more_configs', function (Blueprint $table) {
      $table->id();
      $table->string('mark', 100)->index();
      $table->string('type', 20)->index();
      $table->string('desc', 50);
      $table->longText('config');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('more_configs');
  }
};
