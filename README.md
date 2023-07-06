# Horus

[![codecov](https://codecov.io/gh/hans-thomas/horus/branch/master/graph/badge.svg?token=X1D6I0JLSZ)](https://codecov.io/gh/hans-thomas/horus)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/hans-thomas/horus/php.yml)
![GitHub top language](https://img.shields.io/github/languages/top/hans-thomas/horus)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/hans-thomas/horus)

Horus is a roles and permissions registerer that make working on authorization easy.

Features:

- Integrated with [spatie/laravel-permission](https://github.com/spatie/laravel-permission)
- Supported by [Sphinx](https://github.com/hans-thomas/sphinx)
- Batch creation of roles and permissions
- Create permissions for a model based on related policy class
- Assign permissions to roles in a breeze

## Installation

First install the package via composer

```shell
composer require hans-thomas/horus:^1.0
```

Then, publish the configuration file

```shell
php artisan vendor:publish --tag horus-config
```

Done.

Support
-------

- [Documentation](https://docs-horus.vercel.app)
- [Report bugs](https://github.com/hans-thomas/horus/issues)
