<?php


	namespace Hans\Horus\Traits;


	use Illuminate\Support\Str;

	trait Utilities {
		private string $splitter = '-';

		/**
		 * detect model and normalize the model name
		 *
		 * @param $key
		 * @param $datum
		 *
		 * @return string
		 */
		private function getModel( $key, $datum ): string {
			return $this->normalizeModelName( is_string( $key ) ? $key : $datum );
		}

		/**
		 * normalize the model name
		 *
		 * @param string $model
		 *
		 * @return string
		 */
		private function normalizeModelName( string $model ): string {
			$model = array_reverse( explode( '\\', strtolower( $model ) ) )[ 0 ];

			if ( config( 'horus.prefix_permission' ) === true ) {
				$model = array_reverse( explode( '\\', strtolower( $model ) ) )[ 1 ] . '-' . $model;
			}

			return $model;
		}

		/**
		 * generate permissions with basics
		 *
		 * @param string $model
		 * @param        $datum
		 * @param null   $area
		 *
		 * @return array
		 */
		private function generatePermissions( string $model, $datum, $area ): array {
			$permissions = collect();
			if ( is_array( $datum ) and in_array( '*', $datum ) ) {
				$permissions->push( $this->_generateBasicPermissions( $model, $area ) );
			}
			$permissions->push( $this->_generateAdditionalPermissions( $model, $datum, $area ) );

			return $permissions->flatten(1)->toArray();
		}

		/**
		 * generate permission without basics
		 *
		 * @param string $model
		 * @param        $datum
		 * @param null   $area
		 *
		 * @return array
		 */
		private function generatePermission( string $model, $datum, $area ): array {
			return $this->_generateAdditionalPermissions( $model, $datum, $area );
		}


		/**
		 * generate basics permissions
		 *
		 * @param string $model
		 * @param        $area
		 *
		 * @return array
		 */
		private function _generateBasicPermissions( string $model, $area ): array {
			foreach ( self::keys() as $key ) {
				$permissions[] = [
					'name' => $model . $this->splitter . $key,
					'area' => $area
				];
			}

			return $permissions ?? [];
		}

		/**
		 * generate additional permissions
		 *
		 * @param string $model
		 * @param        $datum
		 * @param        $area
		 *
		 * @return array
		 */
		private function _generateAdditionalPermissions( string $model, $datum, $area ): array {
			if ( is_array( $datum ) ) {
				foreach ( $datum as $additionalPermission ) {
					$permissions[] = [
						'name' => $model . $this->splitter . $additionalPermission,
						'area' => $area
					];
				}
			} else if ( is_string( $datum ) and ! Str::contains( $datum, '\\' ) ) {
				$permissions[] = [
					'name' => $model . $this->splitter . $datum,
					'area' => $area
				];
			}

			return $permissions ?? [];
		}

		protected static function keys(): array {
			return config( 'horus.basic_permissions' );

		}
	}
