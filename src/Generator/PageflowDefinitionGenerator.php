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

namespace PHPMentors\PageflowerBundle\Generator;

use Doctrine\Common\Annotations\Reader;
use PHPMentors\PageflowerBundle\Annotation\EndPage;
use PHPMentors\PageflowerBundle\Annotation\Page;
use PHPMentors\PageflowerBundle\Annotation\PageAnnotationInterface;
use PHPMentors\PageflowerBundle\Annotation\StartPage;
use PHPMentors\PageflowerBundle\Annotation\Transition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class PageflowDefinitionGenerator
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
    public function __construct(ContainerBuilder $container, \ReflectionClass $controllerClass, $controllerServiceId, Reader $reader, array &$pages)
    {
        $this->container = $container;
        $this->controllerClass = $controllerClass;
        $this->controllerServiceId = $controllerServiceId;
        $this->reader = $reader;
        $this->pages = &$pages;
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

        $pageflowBuilderDefinition = class_exists('Symfony\Component\DependencyInjection\ChildDefinition') ? new ChildDefinition('phpmentors_pageflower.pageflow_builder') : new DefinitionDecorator('phpmentors_pageflower.pageflow_builder');
        $pageflowBuilderDefinition->setArguments(array($this->controllerServiceId));

        $startPageFound = false;
        $endPageFound = false;
        $transitions = array();
        foreach ($pageflowAnnotation->value as $page) {
            if (!($page instanceof PageAnnotationInterface)) {
                throw new \LogicException(sprintf(
                    'The value for annotation "%s" should be one of [ %s ], "%s" is specified.',
                    get_class($pageflowAnnotation),
                    implode(', ', array('"PHPMentors\PageflowerBundle\Annotation\Page"', '"PHPMentors\PageflowerBundle\Annotation\StartPage"', '"PHPMentors\PageflowerBundle\Annotation\EndPage"')),
                    is_object($page) ? get_class($page) : $page
                ));
            }

            if (($page instanceof StartPage) || ($page instanceof Page)) {
                if ($page instanceof StartPage) {
                    if ($startPageFound) {
                        throw new \LogicException(sprintf('Annotation "%s" should be specified only once.', get_class($page)));
                    }
                    $startPageFound = true;
                }

                if ($page->value[0] === null) {
                    throw new \LogicException(sprintf('The first element for annotation "%s" cannot be empty.', get_class($page)));
                }

                if (!is_string($page->value[0])) {
                    throw new \LogicException(sprintf('The first element for annotation "%s" should be string, "%s" is specified.', get_class($page), var_export($page->value[0], true)));
                }

                if (empty($page->value[0])) {
                    throw new \LogicException(sprintf('The first element for annotation "%s" cannot be empty.', get_class($page)));
                }

                if ($page instanceof StartPage) {
                    $pageflowBuilderDefinition->addMethodCall('setStartPage', array($page->value[0]));
                } else {
                    $pageflowBuilderDefinition->addMethodCall('addPage', array($page->value[0]));
                }

                $this->pages[] = $page->value[0];

                if (count(array_slice($page->value, 1)) == 0) {
                    throw new \LogicException(sprintf(
                        'Annotation "%s" should be specified at least once in annotation "%s".',
                        'PHPMentors\PageflowerBundle\Annotation\Transition',
                        get_class($page)
                    ));
                }

                foreach (array_slice($page->value, 1) as $transition) {
                    if (!($transition instanceof Transition)) {
                        throw new \LogicException(sprintf(
                            'The second and the subsequent element for annotation "%s" should be "%s", "%s" is specified.',
                            get_class($page),
                            'PHPMentors\PageflowerBundle\Annotation\Transition',
                            var_export($transition, true)
                        ));
                    }

                    if ($transition->value === null) {
                        throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($transition)));
                    }

                    if (!is_string($transition->value)) {
                        throw new \LogicException(sprintf(
                            'The value for annotation "%s" should be string, "%s" is specified.',
                            get_class($transition),
                            var_export($page->value[0], true)
                        ));
                    }

                    if (empty($transition->value)) {
                        throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($transition)));
                    }

                    $transitions[] = array($page->value[0], $transition->value);
                }
            } elseif ($page instanceof EndPage) {
                $endPageFound = true;

                if ($page->value === null) {
                    throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($page)));
                }

                if (is_string($page->value)) {
                    if (empty($page->value)) {
                        throw new \LogicException(sprintf('The value for annotation "%s" cannot be empty.', get_class($page)));
                    }

                    $pageflowBuilderDefinition->addMethodCall('addEndPage', array($page->value));
                    $this->pages[] = $page->value;
                } elseif (is_array($page->value)) {
                    if (!(count($page->value) == 1 && is_string($page->value[0]))) {
                        throw new \LogicException(sprintf(
                            'The value for annotation "%s" should be string or single value array, "%s" is specified.',
                            get_class($page),
                            var_export($page->value, true)
                        ));
                    }

                    if (empty($page->value)) {
                        throw new \LogicException(sprintf('The value for the first element for annotation "%s" cannot be empty.', get_class($page)));
                    }

                    $pageflowBuilderDefinition->addMethodCall('addEndPage', array($page->value[0]));
                    $this->pages[] = $page->value[0];
                } else {
                    throw new \LogicException(sprintf(
                        'The value for annotation "%s" should be string or single value array, "%s" is specified.',
                        get_class($page),
                        var_export($page->value, true)
                    ));
                }
            }
        }

        if (!$startPageFound) {
            throw new \LogicException(sprintf('Annotation "%s" should be specified.', 'PHPMentors\PageflowerBundle\Annotation\StartPage'));
        }

        if (!$endPageFound) {
            throw new \LogicException(sprintf('Annotation "%s" should be specified at least once.', 'PHPMentors\PageflowerBundle\Annotation\EndPage'));
        }

        foreach ($transitions as $transition) {
            if (!in_array($transition[1], $this->pages)) {
                throw new \LogicException(sprintf(
                    'The value for annotation "%s" must be a one of [ %s ], "%s" is specified.',
                    'PHPMentors\PageflowerBundle\Annotation\Transition',
                    implode(', ', array_map(function ($pageId) { return sprintf('"%s"', $pageId); }, $this->pages)),
                    $transition[1]
                ));
            }

            $pageflowBuilderDefinition->addMethodCall('addTransition', $transition);
        }

        $pageflowBuilderServiceId = 'phpmentors_pageflower.pageflow_builder.'.$this->controllerServiceId;
        $this->container->setDefinition($pageflowBuilderServiceId, $pageflowBuilderDefinition);

        $pageflowFactory = $this->container->getDefinition('phpmentors_pageflower.pageflow')->getFactory();
        $pageflowDefinition = class_exists('Symfony\Component\DependencyInjection\ChildDefinition') ? new ChildDefinition('phpmentors_pageflower.pageflow') : new DefinitionDecorator('phpmentors_pageflower.pageflow');
        $pageflowDefinition->setFactory(array(new Reference($pageflowBuilderServiceId), $pageflowFactory[1]));
        $pageflowServiceId = 'phpmentors_pageflower.pageflow.'.$this->controllerServiceId;
        $this->container->setDefinition($pageflowServiceId, $pageflowDefinition);
        $this->container->getDefinition('phpmentors_pageflower.pageflow_repository')->addMethodCall('add', array(new Reference($pageflowServiceId)));

        if (method_exists($this->container, 'addObjectResource')) {
            $this->container->addObjectResource($this->controllerClass);
        } else {
            $this->container->addClassResource($this->controllerClass);
        }
    }
}
