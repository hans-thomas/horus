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
			Schema::create( 'permissions', function( Blueprint $table ) {
				$table->id();
				$table->string( 'name', 128 );
				$table->string( 'area', 128 );

				$table->unique( [ 'name', 'area' ] );
				$table->timestamps();
			} );
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down(): void {
			Schema::dropIfExists( 'permissions' );
		}

	};
