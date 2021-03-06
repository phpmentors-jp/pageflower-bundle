# PageflowerBundle

A pageflow engine for Symfony applications

[![Total Downloads](https://poser.pugx.org/phpmentors/pageflower-bundle/downloads.png)](https://packagist.org/packages/phpmentors/pageflower-bundle)
[![Latest Stable Version](https://poser.pugx.org/phpmentors/pageflower-bundle/v/stable.png)](https://packagist.org/packages/phpmentors/pageflower-bundle)
[![Latest Unstable Version](https://poser.pugx.org/phpmentors/pageflower-bundle/v/unstable.png)](https://packagist.org/packages/phpmentors/pageflower-bundle)
[![Build Status](https://travis-ci.org/phpmentors-jp/pageflower-bundle.svg?branch=master)](https://travis-ci.org/phpmentors-jp/pageflower-bundle)

## Features

* Annotation-based page flow definition
* Conversation management
* Access-controlled actions
* Conversation-scoped properties
* User-defined methods to be called immediately after a conversation has started
* Support for multiple browser windows or tabs

## Installation

`PHPMentorsPageflowerBundle` can be installed using [Composer](http://getcomposer.org/).

First, add the dependency to `phpmentors/pageflower-bundle` into your `composer.json` file as the following:

Stable version:

```
composer require phpmentors/pageflower-bundle "1.5.*"
```

Development version:

```
composer require phpmentors/pageflower-bundle "~1.6@dev"
```

Second, add `PHPMentorsPageflowerBundle` into your bundles to register in `AppKernel::registerBundles()` as the following:

```php
...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new PHPMentors\PageflowerBundle\PHPMentorsPageflowerBundle(),
        );
        ...
```

## Support

If you find a bug or have a question, or want to request a feature, create an issue or pull request for it on [Issues](https://github.com/phpmentors-jp/pageflower-bundle/issues).

## Copyright

Copyright (c) 2014-2015, 2018-2020 Atsuhiro Kubo, All rights reserved.

## License

[The BSD 2-Clause License](http://opensource.org/licenses/BSD-2-Clause)
