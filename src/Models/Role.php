<?php

	namespace Hans\Horus\Models;

	use Hans\Horus\Exceptions\HorusErrorCode;
	use Hans\Horus\Exceptions\HorusException;
	use Hans\Horus\HasPermissions;
	use Hans\Horus\Models\Contracts\Role as RoleContract;
	use HorusCacheEnum;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsToMany;
	use Illuminate\Support\Facades\Cache;
	use Symfony\Component\HttpFoundation\Response as ResponseAlias;

	class Role extends Model implements RoleContract {
		use HasPermissions;

		protected $fillable = [ 'name', 'area', 'version' ];
		protected $casts = [ 'version' => 'integer' ];

		protected static function booted() {
			static::created( function( self $model ) {
				Cache::forever( HorusCacheEnum::ROLE . $model->id, $model->fresh() );
			} );
			static::updated( function( self $model ) {
				$model->increaseVersion();
			} );
			static::deleted( function( self $model ) {
				Cache::forget( HorusCacheEnum::ROLE . $model->id );
			} );
		}


		public function permissions(): BelongsToMany {
			return $this->belongsToMany( Permission::class );
		}

		/**
		 * @param string      $name
		 * @param string|null $area
		 *
		 * @return static
		 * @throws HorusException
		 */
		public static function findByName( string $name, string $area = null ): self {
			$role = self::query();
			if ( $area ) {
				$role = $role->whereArea( $area );
			}
			$role = $role->whereName( $name )->first();
			if ( ! $role ) {
				$area = ! $area ? 'all areas' : $area . ' area';
				throw new HorusException( "The $name role not found in the $area!",
					HorusErrorCode::ROLE_NOT_FOUND, ResponseAlias::HTTP_NOT_FOUND );
			}

			return $role;
		}

		/**
		 * @param int         $id
		 * @param string|null $area
		 *
		 * @return static
		 * @throws HorusException
		 */
		public static function findById( int $id, string $area = null ): self {
			$role = self::query();
			if ( $area ) {
				$role = $role->whereArea( $area );
			}

			$role = $role->find( $id );

			if ( ! $role ) {
				$area = ! $area ? 'all areas' : $area . ' area';
				throw new HorusException( "The $id role not found in the $area!",
					HorusErrorCode::ROLE_NOT_FOUND, ResponseAlias::HTTP_NOT_FOUND );
			}

			return $role;
		}

		/**
		 * @param string $name
		 * @param string $area
		 *
		 * @return self
		 */
		public static function findOrCreate( string $name, string $area ): self {
			$role = self::whereName( $name )->whereArea( $area )->first();

			if ( $role ) {
				return $role;
			}

			return self::create( compact( 'name', 'area' ) );
		}

		/**
		 * @param int $id
		 *
		 * @return Role
		 */
		public static function findAndCache( int $id ): self {
			return Cache::rememberForever(
				HorusCacheEnum::ROLE . $id,
				fn() => self::query()->findOrFail( $id )
			);
		}

		public function getVersion(): int {
			return $this->version ? : $this->refresh()->version;
		}

		public function increaseVersion(): bool {
			try {
				$this->fill( [ 'version' => $this->getVersion() + 1 ] )->saveQuietly();
				Cache::forget( HorusCacheEnum::ROLE . $this->id );
				Cache::forever( HorusCacheEnum::ROLE . $this->id, $this );
			} catch ( \Throwable $e ) {
				return false;
			}

			return true;
		}
	}
