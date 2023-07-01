<?php

	use Hans\Horus\Helpers\Enums\AreaEnum;
	use Hans\Horus\Helpers\Enums\RoleEnum;

	if ( ! function_exists( 'roles' ) ) {
		/**
		 * @return array
		 */
		function roles(): array {
			return RoleEnum::cases();
		}
	}

	if ( ! function_exists( 'areas' ) ) {
		/**
		 * @return array
		 */
		function areas(): array {
			return AreaEnum::cases();
		}
	}
