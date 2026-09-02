<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single-row table: the identity block every page reads from.
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('headline');
            $table->string('title');
            $table->text('summary');

            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('phone_link')->nullable();
            $table->string('location')->nullable();
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('resume_path')->nullable();

            $table->json('languages')->nullable();   // [{name, level}]
            $table->json('strengths')->nullable();   // string[]
            $table->json('seo')->nullable();         // {title, description, og_image}

            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
