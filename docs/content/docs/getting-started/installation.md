---
title: 'Installation'
date: 2023-07-06
weight: 1
summary: Guidance to install horus package.
---

First install the package via composer

```shell
composer require hans-thomas/horus:^1.0
```

Then, publish the configuration file

```shell
php artisan vendor:publish --tag horus-config
```

Done.