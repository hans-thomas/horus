<?php


	namespace Hans\Horus\Facades;


	use Illuminate\Support\Facades\Facade;
	use RuntimeException;

	/**
	 *
	 */
	class Horus extends Facade {

		/**
		 * Get the registered name of the component.
		 *
		 * @return string
		 *
		 * @throws RuntimeException
		 */
		protected static function getFacadeAccessor(): string {
			return 'horus-service';
		}

	}
