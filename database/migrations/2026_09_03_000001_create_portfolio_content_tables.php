<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Case studies are the centrepiece: one row renders a full narrative page.
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('client_label')->nullable();      // anonymised, e.g. "50+ employee logistics client"
            $table->string('role')->nullable();
            $table->year('year_from')->nullable();
            $table->year('year_to')->nullable();
            $table->text('summary');                          // card + meta description

            // Structured narrative: fixed sections keep every study reading the same way.
            $table->text('context')->nullable();
            $table->text('constraints')->nullable();
            $table->json('options_rejected')->nullable();     // [{option, why_rejected}]
            $table->text('decision')->nullable();
            $table->text('implementation')->nullable();
            $table->json('outcomes')->nullable();             // [{metric, value, note}]
            $table->json('architecture_diagram')->nullable(); // node/edge data for the inline SVG

            $table->json('tech')->nullable();                 // ["Laravel 13", "MySQL 8", ...]
            $table->string('repo_url')->nullable();
            $table->string('live_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 20)->default('draft');   // draft | published
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'sort_order']);
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company');
            $table->string('company_url')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();    // full-time | freelance | contract
            $table->date('started_on');
            $table->date('ended_on')->nullable();             // null = present
            $table->text('summary')->nullable();
            $table->json('highlights')->nullable();           // bullet strings
            $table->json('tech')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->index(['status', 'started_on']);
        });

        Schema::create('skill_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');                           // "Backend & Architecture"
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('years')->nullable(); // shown as "3y", never as a progress bar
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['skill_group_id', 'name']);
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('issuer');
            $table->string('credential_url')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('education_entries', function (Blueprint $table) {
            $table->id();
            $table->string('degree');
            $table->string('institution');
            $table->string('location')->nullable();
            $table->date('started_on')->nullable();
            $table->date('ended_on')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Homepage composition: reorder/toggle sections without a deploy.
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page', 50)->default('home');
            $table->string('key', 50);                        // hero | case_studies | stack | experience | contact
            $table->string('heading')->nullable();
            $table->string('subheading')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page', 'key']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->text('message');
            $table->string('ip_hash', 64)->nullable();        // hashed, never the raw address
            $table->string('user_agent')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('education_entries');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_groups');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('case_studies');
    }
};
