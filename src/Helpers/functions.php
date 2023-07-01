<?php

	if ( ! function_exists( 'horus_config' ) ) {
		function horus_config( string $key, mixed $default = null ) {
			return config( "horus.$key", $default );
		}
	}