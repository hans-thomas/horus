<?php

	namespace Hans\Horus\Tests\Instances\Policies;

	use Hans\Horus\Tests\Instances\Models\Post;
	use Illuminate\Contracts\Auth\Authenticatable;

	class PostPolicy {
		/**
		 * Determine whether the user can view any models.
		 *
		 * @param Authenticatable $user
		 *
		 * @return bool
		 */
		public function viewAny( Authenticatable $user ): bool {
			return $user->can( 'viewAny' );
		}

		/**
		 * Determine whether the user can view the model.
		 *
		 * @param Authenticatable $user
		 * @param Post            $post
		 *
		 * @return bool
		 */
		public function view( Authenticatable $user, Post $post ): bool {
			return $user->can( 'view' );
		}

		/**
		 * Determine whether the user can create models.
		 *
		 * @param Authenticatable $user
		 *
		 * @return bool
		 */
		public function create( Authenticatable $user ): bool {
			return $user->can( 'create' );
		}

		/**
		 * Determine whether the user can update the model.
		 *
		 * @param Authenticatable $user
		 * @param Post            $post
		 *
		 * @return bool
		 */
		public function update( Authenticatable $user, Post $post ): bool {
			return $user->can( 'update' );
		}

		/**
		 * Determine whether the user can delete the model.
		 *
		 * @param Authenticatable $user
		 * @param Post            $post
		 *
		 * @return bool
		 */
		public function delete( Authenticatable $user, Post $post ): bool {
			return $user->can( 'delete' );
		}

		/**
		 * Determine whether the user can restore the model.
		 *
		 * @param Authenticatable $user
		 * @param Post            $post
		 *
		 * @return bool
		 */
		public function restore( Authenticatable $user, Post $post ): bool {
			return $user->can( 'restore' );
		}

		/**
		 * Determine whether the user can permanently delete the model.
		 *
		 * @param Authenticatable $user
		 * @param Post            $post
		 *
		 * @return bool
		 */
		public function forceDelete( Authenticatable $user, Post $post ): bool {
			return $user->can( 'forceDelete' );
		}

		/**
		 * Determine whether the user can view the model.
		 *
		 * @param Authenticatable $user
		 *
		 * @return bool
		 */
		public function viewComments( Authenticatable $user ): bool {
			return $user->can( 'viewComments' );
		}

		/**
		 * Determine whether the user can view the model.
		 *
		 * @param Authenticatable $user
		 * @param Post            $post
		 *
		 * @return bool
		 */
		public function updateComments( Authenticatable $user, Post $post ): bool {
			return $user->can( 'updateComments' ) or $post->writer_id == $user->id;
		}

	}