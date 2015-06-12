<?php
/*
 * Copyright (c) 2014-2015 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsPageflowerBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\PageflowerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PHPMentorsPageflowerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');

        $this->transformConfigToContainerParameters($config, $container);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'phpmentors_pageflower';
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function transformConfigToContainerParameters(array $config, ContainerBuilder $container)
    {
        $container->setParameter('phpmentors_pageflower.conversation_request_parameter_name', $config['conversation']['request_parameter_name']);
        $container->setParameter('phpmentors_pageflower.conversation_session_storage_key', $config['conversation']['session_storage_name']);
        $container->setParameter('phpmentors_pageflower.end_on_next_step_of_end_page', $config['conversation']['end_on_next_step_of_end_page']);
    }
}
