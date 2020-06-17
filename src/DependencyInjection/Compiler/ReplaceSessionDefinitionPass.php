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

class ReplaceSessionDefinitionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $conversationalSessionDefinition = $container->getDefinition('phpmentors_pageflower.conversational_session');
        $sessionDefinition = $container->getDefinition('session');

        /*
         * As of version 3.4.39, the 'session.attribute_bag' and 'session.flash_bag' arguments have been removed from the 'session' definition.
         * See https://github.com/symfony/symfony/pull/36063 for more details.
         */
        if (count($sessionDefinition->getArguments()) == 1) {
            $conversationalSessionDefinition->replaceArgument(1, null);
            $conversationalSessionDefinition->replaceArgument(2, null);
        }

        $container->setDefinition('session', $conversationalSessionDefinition);
        $container->removeDefinition('phpmentors_pageflower.conversational_session');
    }
}
