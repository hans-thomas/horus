<?php


	namespace Hans\Horus\Facades;


	use Illuminate\Support\Facades\Facade;
	use RuntimeException;

	/**
	 *
	 */
	class Seeder extends Facade {

		/**
		 * Get the registered name of the component.
		 *
		 * @return string
		 *
		 * @throws RuntimeException
		 */
		protected static function getFacadeAccessor(): string {
			return 'seeder-service';
		}

	}
