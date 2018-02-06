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

namespace PHPMentors\PageflowerBundle\Controller;

use PHPMentors\DomainKata\Entity\EntityInterface;

class ReflectionConversationalController implements EntityInterface
{
    /**
     * @var array
     */
    private $acceptablePages = array();

    /**
     * @var \ReflectionClass
     */
    private $class;

    /**
     * @var \ReflectionMethod[]
     */
    private $initMethods = array();

    /**
     * @var \ReflectionProperty[]
     */
    private $statefulProperties = array();

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = new \ReflectionClass($class);
    }

    /**
     * @param string $method
     * @param string $pageId
     */
    public function addAcceptablePage($method, $pageId)
    {
        if (!array_key_exists($method, $this->acceptablePages)) {
            $this->acceptablePages[$method] = array();
        }

        $this->acceptablePages[$method][] = $pageId;
    }

    /**
     * @param string $method
     *
     * @return array
     */
    public function getAcceptablePages($method)
    {
        if (array_key_exists($method, $this->acceptablePages)) {
            return $this->acceptablePages[$method];
        } else {
            return array();
        }
    }

    /**
     * @return \ReflectionClass
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $initMethod
     */
    public function addInitMethod($initMethod)
    {
        $this->initMethods[] = new \ReflectionMethod($this->class->getName(), $initMethod);
    }

    /**
     * @return \ReflectionMethod[]
     */
    public function getInitMethods()
    {
        return $this->initMethods;
    }

    /**
     * @param string $statefulProperty
     */
    public function addStatefulProperty($statefulProperty)
    {
        $this->statefulProperties[] = new \ReflectionProperty($this->class->getName(), $statefulProperty);
    }

    /**
     * @return \ReflectionProperty[]
     */
    public function getStatefulProperties()
    {
        return $this->statefulProperties;
    }
}
