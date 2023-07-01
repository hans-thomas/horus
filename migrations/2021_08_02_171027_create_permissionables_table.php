<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

	return new class extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up(): void {
            Schema::create( 'permissionables', function( Blueprint $table ) {
                $table->foreignId( 'permission_id' )->constrained();
                $table->unsignedBigInteger( 'permissionable_id' );
                $table->string( 'permissionable_type' );
            } );
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down(): void {
            Schema::dropIfExists( 'permissionables' );
        }

    };
