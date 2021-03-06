<?php
/*
 * Copyright (c) Atsuhiro Kubo <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsPageflowerBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @since Class available since Release 1.1.0
 */
class AppKernel extends Kernel
{
    /**
     * @var \Closure
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new PHPMentors\PageflowerBundle\PHPMentorsPageflowerBundle(),
            new PHPMentors\PageflowerBundle\Controller\Bundle\TestBundle\TestBundle(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');

        if ($this->config instanceof \Closure) {
            $loader->load($this->config);
        }
    }

    /**
     * @param \Closure $config
     */
    public function setConfig(\Closure $config)
    {
        $this->config = $config;
    }
}
