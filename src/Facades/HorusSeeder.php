<?php


	namespace Hans\Horus\Facades;


	use Illuminate\Support\Facades\Facade;

	class HorusSeeder extends Facade {
		protected static function getFacadeAccessor() {
			return \Hans\Horus\HorusSeeder::class;
		}
	}
