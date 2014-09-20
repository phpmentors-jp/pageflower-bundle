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

namespace PHPMentors\PageflowerBundle\Generator;

use Doctrine\Common\Annotations\Reader;
use Stagehand\FSM\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

use PHPMentors\PageflowerBundle\Annotation\AnnotationInterface;
use PHPMentors\PageflowerBundle\Annotation\End;
use PHPMentors\PageflowerBundle\Annotation\Start;
use PHPMentors\PageflowerBundle\Annotation\States;
use PHPMentors\PageflowerBundle\Annotation\Transitions;

class PageflowDefinitionGenerator
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
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
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @var array
     */
    private $states;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \ReflectionClass                                        $controllerClass
     * @param string                                                  $controllerServiceId
     * @param \Doctrine\Common\Annotations\Reader                     $reader
     * @param array                                                   $states
     */
    public function __construct(ContainerBuilder $container, \ReflectionClass $controllerClass, $controllerServiceId, Reader $reader, array &$states)
    {
        $this->container = $container;
        $this->controllerClass = $controllerClass;
        $this->controllerServiceId = $controllerServiceId;
        $this->reader = $reader;
        $this->states = &$states;
    }

    /**
     * @throws \LogicException
     */
    public function generate()
    {
        $pageflowAnnotation = $this->reader->getClassAnnotation($this->controllerClass, 'PHPMentors\PageflowerBundle\Annotation\Pageflow');
        if ($pageflowAnnotation === null) {
            throw new \LogicException(sprintf(
                'Annotation "%s" is not found in class "%s".',
                'PHPMentors\PageflowerBundle\Annotation\Pageflow',
                $this->controllerClass->getName()
            ));
        }

        $pageflowBuilderDefinition = new DefinitionDecorator('phpmentors_pageflower.pageflow_builder');
        $pageflowBuilderDefinition->setArguments(array($this->controllerServiceId));

        $statesFound = false;
        $transitionsFound = false;
        foreach ($pageflowAnnotation->value as $annotation) {
            if ($annotation instanceof States) {
                if ($statesFound) {
                    throw new \LogicException(sprintf('Annotation "%s" must be specified only once.', get_class($annotation)));
                }
                $statesFound = true;

                foreach ($annotation->value as $state) {
                    if ($state instanceof AnnotationInterface) {
                        if ($state->value === null || strlen($state->value) == 0) {
                            throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($state)));
                        }

                        if ($state instanceof Start) {
                            $pageflowBuilderDefinition->addMethodCall('addState', array($state->value));
                            $pageflowBuilderDefinition->addMethodCall('setStartState', array($state->value));
                            $this->states[] = $state->value;
                        } elseif ($state instanceof End) {
                            $pageflowBuilderDefinition->addMethodCall('addState', array($state->value));
                            $pageflowBuilderDefinition->addMethodCall('setEndState', array($state->value, StateInterface::STATE_FINAL));
                            $this->states[] = $state->value;
                        } else {
                            throw new \LogicException(sprintf(
                                'State "%s" must be encapsulated with one of [ %s ], "%s" is specified.',
                                $state->value,
                                implode(', ', array('PHPMentors\PageflowerBundle\Annotation\Start', 'PHPMentors\PageflowerBundle\Annotation\End')),
                                get_class($state)
                            ));
                        }
                    } else {
                        if ($state === null || strlen($state) == 0) {
                            throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($annotation)));
                        }

                        $pageflowBuilderDefinition->addMethodCall('addState', array($state));
                        $this->states[] = $state;
                    }
                }
            } elseif ($annotation instanceof Transitions) {
                if ($transitionsFound) {
                    throw new \LogicException(sprintf('Annotation "%s" must be specified only once.', get_class($annotation)));
                }
                $transitionsFound = true;

                foreach ($annotation->value as $transition) {
                    if (is_array($transition) && count($transition) == 2 && is_string($transition[0]) && is_string($transition[1])) {
                        foreach (array($transition[0], $transition[1]) as $state) {
                            if ($state === null || strlen($state) == 0) {
                                throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($annotation)));
                            }

                            if (!in_array($state, $this->states)) {
                                throw new \LogicException(sprintf(
                                    'The value for annotation "%s" must be one of [ %s ], "%s" is specified.',
                                    get_class($annotation),
                                    implode(', ', $this->states),
                                    $state
                                ));
                            }
                        }

                        $pageflowBuilderDefinition->addMethodCall('addTransition', array($transition[0], $transition[1], $transition[1]));
                    } else {
                        throw new \LogicException(sprintf(
                            'The value for annotation "%s" must be string array, "%s" is specified.',
                            get_class($annotation),
                            var_export($transition, true)
                        ));
                    }
                }
            } else {
                throw new \LogicException(sprintf(
                    'The value for annotation "%s" must be one of [ %s ], "%s" is specified.',
                    get_class($pageflowAnnotation),
                    implode(', ', array('"PHPMentors\PageflowerBundle\Annotation\States"', '"PHPMentors\PageflowerBundle\Annotation\Transitions"')),
                    is_object($annotation) ? get_class($annotation) : $annotation
                ));
            }
        }

        $pageflowBuilderServiceId = 'phpmentors_pageflower.pageflow_builder.' . $this->controllerServiceId;
        $this->container->setDefinition($pageflowBuilderServiceId, $pageflowBuilderDefinition);

        $pageflowDefinition = new DefinitionDecorator('phpmentors_pageflower.pageflow');
        $pageflowDefinition->setFactoryService($pageflowBuilderServiceId);
        $pageflowServiceId = 'phpmentors_pageflower.pageflow.' . $this->controllerServiceId;
        $this->container->setDefinition($pageflowServiceId, $pageflowDefinition);
        $this->container->getDefinition('phpmentors_pageflower.pageflow_repository')->addMethodCall('add', array(new Reference($pageflowServiceId)));

        $this->container->addClassResource($this->controllerClass);
    }
}
