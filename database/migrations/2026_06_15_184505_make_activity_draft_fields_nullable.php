<?php

use App\Enums\ActivityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            Schema::dropIfExists('activities_new');

            Schema::create('activities_new', function (Blueprint $table) {
                $table->id();
                $table->string('title_nl');
                $table->string('title_fr');
                $table->text('content_nl')->nullable();
                $table->text('content_fr')->nullable();
                $table->enum('activity_type', array_keys(ActivityType::getOptionsArray()))->default(ActivityType::KIDICALMASS->value);
                $table->dateTime('begin_date');
                $table->string('location')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('distance')->nullable();
                $table->string('komoot_url')->nullable();
                $table->string('commute_link')->nullable();
                $table->integer('duration_minutes')->nullable();
                $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('organizer_id')->nullable()->constrained('users')->onDelete('set null');
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });

            $columns = implode(', ', [
                'id', 'title_nl', 'title_fr', 'content_nl', 'content_fr',
                'activity_type', 'begin_date', 'location', 'postal_code',
                'distance', 'komoot_url', 'commute_link', 'duration_minutes',
                'author_id', 'organizer_id', 'is_published', 'created_at', 'updated_at',
            ]);

            DB::statement("INSERT INTO activities_new ({$columns}) SELECT {$columns} FROM activities");
            DB::statement('DROP TABLE activities');
            DB::statement('ALTER TABLE activities_new RENAME TO activities');
        } else {
            DB::statement('ALTER TABLE activities MODIFY COLUMN content_nl text NULL');
            DB::statement('ALTER TABLE activities MODIFY COLUMN content_fr text NULL');
            DB::statement('ALTER TABLE activities MODIFY COLUMN location varchar(255) NULL');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            Schema::dropIfExists('activities_old');

            Schema::create('activities_old', function (Blueprint $table) {
                $table->id();
                $table->string('title_nl');
                $table->string('title_fr');
                $table->text('content_nl');
                $table->text('content_fr');
                $table->enum('activity_type', array_keys(ActivityType::getOptionsArray()))->default(ActivityType::KIDICALMASS->value);
                $table->dateTime('begin_date');
                $table->string('location');
                $table->string('postal_code')->nullable();
                $table->string('distance')->nullable();
                $table->string('komoot_url')->nullable();
                $table->string('commute_link')->nullable();
                $table->integer('duration_minutes')->nullable();
                $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('organizer_id')->nullable()->constrained('users')->onDelete('set null');
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });

            $columns = implode(', ', [
                'id', 'title_nl', 'title_fr', 'content_nl', 'content_fr',
                'activity_type', 'begin_date', 'location', 'postal_code',
                'distance', 'komoot_url', 'commute_link', 'duration_minutes',
                'author_id', 'organizer_id', 'is_published', 'created_at', 'updated_at',
            ]);

            DB::statement("INSERT INTO activities_old ({$columns}) SELECT {$columns} FROM activities");
            DB::statement('DROP TABLE activities');
            DB::statement('ALTER TABLE activities_old RENAME TO activities');
        } else {
            DB::statement('ALTER TABLE activities MODIFY COLUMN content_nl text NOT NULL');
            DB::statement('ALTER TABLE activities MODIFY COLUMN content_fr text NOT NULL');
            DB::statement('ALTER TABLE activities MODIFY COLUMN location varchar(255) NOT NULL');
        }
    }
};
