<?php

	if ( ! function_exists( 'horus_config' ) ) {
		/**
		 * Return requested key from horus config
		 *
		 * @param string     $key
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		function horus_config( string $key, mixed $default = null ): mixed {
			return config( "horus.$key", $default );
		}
	}