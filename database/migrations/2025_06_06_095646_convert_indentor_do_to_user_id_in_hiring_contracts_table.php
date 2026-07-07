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
        Schema::table('hiring_contracts', function (Blueprint $table) {
            // Rename or drop the old text-based column
            $table->dropColumn('indentor_do');

            // Add new foreign key column
            $table->unsignedBigInteger('indentor_do')->nullable()->after('indenting_section');
            $table->foreign('indentor_do')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hiring_contracts', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['indentor_do']);
            $table->dropColumn('indentor_do');

            // Re-add old text field
            $table->string('indentor_do')->nullable()->after('indenting_section');
        });
    }
};
