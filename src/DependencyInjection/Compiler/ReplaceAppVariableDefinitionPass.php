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

namespace PHPMentors\PageflowerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ReplaceAppVariableDefinitionPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('twig.app_variable')) {
            foreach ($container->getDefinition('twig.app_variable')->getMethodCalls() as $methodCall) {
                list($method, $arguments) = $methodCall;
                $container->getDefinition('phpmentors_pageflower.conversational_app_variable')->addMethodCall($method, $arguments);
            }

            $container->setAlias('twig.app_variable', 'phpmentors_pageflower.conversational_app_variable');
        }
    }
}
