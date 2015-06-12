<?php
/*
 * Copyright (c) 2015 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsPageflowerBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

/**
 * @since File available since Release 1.1.0
 */
error_reporting(E_ALL);

$loader = require dirname(__DIR__).'/vendor/autoload.php'; /* @var $loader \Composer\Autoload\ClassLoader */

Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
