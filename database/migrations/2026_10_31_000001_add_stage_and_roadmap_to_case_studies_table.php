<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            $table->string('stage', 20)->nullable()->after('live_url'); // live | staging | development
            $table->json('roadmap')->nullable()->after('stage');        // string[] — "What's next"
        });
    }

    public function down(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            $table->dropColumn(['stage', 'roadmap']);
        });
    }
};
