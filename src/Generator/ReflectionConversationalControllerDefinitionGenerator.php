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

namespace PHPMentors\PageflowerBundle\Generator;

use Doctrine\Common\Annotations\Reader;
use PHPMentors\PageflowerBundle\Annotation\Accept;
use PHPMentors\PageflowerBundle\Annotation\Init;
use PHPMentors\PageflowerBundle\Annotation\Stateful;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class ReflectionConversationalControllerDefinitionGenerator
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var string
     */
    private $controllerClass;

    /**
     * @var string
     */
    private $controllerServiceId;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array
     */
    private $pages;

    /**
     * @param ContainerBuilder $container
     * @param \ReflectionClass $controllerClass
     * @param string           $controllerServiceId
     * @param Reader           $reader
     * @param array            $pages
     */
    public function __construct(ContainerBuilder $container, \ReflectionClass $controllerClass, $controllerServiceId, Reader $reader, array $pages)
    {
        $this->container = $container;
        $this->controllerClass = $controllerClass;
        $this->controllerServiceId = $controllerServiceId;
        $this->reader = $reader;
        $this->pages = $pages;
    }

    /**
     * @throws \LogicException
     */
    public function generate()
    {
        $definition = class_exists('Symfony\Component\DependencyInjection\ChildDefinition') ? new ChildDefinition('phpmentors_pageflower.reflection_conversational_controller') : new DefinitionDecorator('phpmentors_pageflower.reflection_conversational_controller');
        $definition->setArguments(array($this->controllerClass->getName()));

        foreach ($this->controllerClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) { /* @var $method \ReflectionMethod */
            if ($method->getDeclaringClass()->getName() == $this->controllerClass->getName()) {
                foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
                    if ($annotation instanceof Init) {
                        $definition->addMethodCall('addInitMethod', array($method->getName()));
                    } elseif ($annotation instanceof Accept) {
                        if ($annotation->value === null || (is_string($annotation->value) && strlen($annotation->value) == 0)) {
                            throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($annotation)));
                        }

                        foreach ((array) $annotation->value as $accept) {
                            if ($accept === null || strlen($accept) == 0) {
                                throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($annotation)));
                            }

                            if (!in_array($accept, $this->pages)) {
                                throw new \LogicException(sprintf(
                                    'The value for annotation "%s" must be a one of [ %s ], "%s" is specified.',
                                    get_class($annotation),
                                    implode(', ', array_map(function ($pageId) {
                                        return sprintf('"%s"', $pageId);
                                    }, $this->pages)),
                                    $accept
                                ));
                            }

                            $definition->addMethodCall('addAcceptablePage', array($method->getName(), $accept));
                        }
                    }
                }
            }
        }

        foreach ($this->controllerClass->getProperties() as $property) { /* @var $property \ReflectionProperty */
            if ($property->getDeclaringClass()->getName() == $this->controllerClass->getName() && !$property->isStatic()) {
                foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                    if ($annotation instanceof Stateful) {
                        $definition->addMethodCall('addStatefulProperty', array($property->getName()));
                    }
                }
            }
        }

        $reflectionConversationalControllerServiceId = 'phpmentors_pageflower.reflection_conversational_controller.'.$this->controllerServiceId;
        $this->container->setDefinition($reflectionConversationalControllerServiceId, $definition);

        $this->container->getDefinition('phpmentors_pageflower.reflection_conversational_controller_repository')->addMethodCall('add', array(new Reference($reflectionConversationalControllerServiceId)));
    }
}
