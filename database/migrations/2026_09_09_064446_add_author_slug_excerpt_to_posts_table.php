<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique()->after('title');
            $table->string('excerpt')->nullable()->after('body');
            $table->string('featured_image')->nullable()->after('excerpt');
            $table->index(['user_id', 'created_at']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'slug', 'excerpt', 'featured_image']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['slug']);
        });
    }
};
