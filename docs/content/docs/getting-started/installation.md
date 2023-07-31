---
title: 'Installation'
date: 2023-07-06
weight: 1
summary: Guidance to install horus package.
---

<p><img alt="horus banner" src="/images/horus-banner.png"></p>

[![codecov](https://codecov.io/gh/hans-thomas/horus/branch/master/graph/badge.svg?token=X1D6I0JLSZ)](https://codecov.io/gh/hans-thomas/horus)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/hans-thomas/horus/php.yml)
![GitHub top language](https://img.shields.io/github/languages/top/hans-thomas/horus)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/hans-thomas/horus)
![StyleCi](https://github.styleci.io/repos/464497597/shield?style=plastic)

First install the package via composer.

```shell
composer require hans-thomas/horus:^1.0
```

Then, publish the configuration file.

```shell
php artisan vendor:publish --tag horus-config
```

Done.