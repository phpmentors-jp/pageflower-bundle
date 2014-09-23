<?php
/*
 * Copyright (c) 2014 KUBO Atsuhiro <kubo@iteman.jp>,
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

use PHPMentors\PageflowerBundle\Generator\PageflowDefinitionGenerator;
use PHPMentors\PageflowerBundle\Generator\ReflectionConversationalControllerDefinitionGenerator;

class GeneratePageflowDefinitionsPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('phpmentors_pageflower.pageflow') as $controllerServiceId => $attribute) {
            $controllerClass = new \ReflectionClass($container->getParameterBag()->resolveValue($container->getDefinition($controllerServiceId)->getClass()));
            if (!$controllerClass->implementsInterface('PHPMentors\PageflowerBundle\Controller\ConversationalControllerInterface')) {
                throw new \LogicException(sprintf(
                    'The controller "%s" must implement "%s".',
                    $controllerClass->getName(),
                    'PHPMentors\PageflowerBundle\Controller\ReflectionConversationalController'
                ));
            }

            $pages = array();
            $pageflowDefinitionGenerator = new PageflowDefinitionGenerator($container, $controllerClass, $controllerServiceId, $container->get('annotation_reader'), $pages);
            $pageflowDefinitionGenerator->generate();

            $reflectionConversationalControllerDefinitionGenerator = new ReflectionConversationalControllerDefinitionGenerator($container, $controllerClass, $controllerServiceId, $container->get('annotation_reader'), $pages);
            $reflectionConversationalControllerDefinitionGenerator->generate();
        }
    }
}
