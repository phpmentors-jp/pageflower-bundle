# PHPMentorsPageflowerBundle

A pageflow engine for Symfony applications

## Features

## Installation

Composer Console can be installed using [Composer](http://getcomposer.org/).

First, add the dependency to **phpmentors/pageflower-bundle** into your **composer.json** file as the following:

```json
{
    "require": {
        "phpmentors/pageflower-bundle": "~1.0@dev"
    },
}
```

Second, update your dependencies as the following:

```console
composer update phpmentors/pageflower-bundle
```

## Configuration

app/AppKernel.php:

```php
...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new PHPMentors\PageflowerBundle\PHPMentorsPageflowerBundle(),
            ...
        );
        ...
```

## Support

If you find a bug or have a question, or want to request a feature, create an issue or pull request for it on [Issues](https://github.com/phpmentors-jp/pageflower-bundle/issues).

## Copyright

Copyright (c) 2014 KUBO Atsuhiro, All rights reserved.

## License

[The BSD 2-Clause License](http://opensource.org/licenses/BSD-2-Clause)
