<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('source_key')->nullable()->unique(); // e.g. Instagram post id from the media folder
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->string('location')->nullable();
            $table->date('event_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });

        Schema::create('work_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')->constrained()->cascadeOnDelete();
            $table->string('type', 16); // image | video | embed
            $table->string('path')->nullable(); // file on the media disk
            $table->string('url')->nullable();  // external image / video / YouTube link
            $table->string('thumb')->nullable();  // small preview for grids
            $table->string('poster')->nullable(); // still frame for videos
            $table->string('caption')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 32)->default('sparkles');
            $table->json('title');
            $table->json('description')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author');
            $table->json('text');
            $table->string('event')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('event_type')->nullable();
            $table->date('event_date')->nullable();
            $table->unsignedInteger('guests')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 16)->default('new'); // new | in_progress | done
            $table->timestamps();
        });

        // Keys already imported from the media folder, so deleted items are not imported again.
        Schema::create('imported_keys', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        foreach (['imported_keys', 'settings', 'inquiries', 'testimonials', 'services', 'work_media', 'works', 'categories'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
