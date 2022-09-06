<?php

	return [
		'defaults'          => [
			'area' => \AreasEnum::USER
		],
		'prefix_permission' => true,
		'basic_permissions' => [ 'viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete' ],
	];
