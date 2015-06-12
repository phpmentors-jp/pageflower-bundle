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

namespace PHPMentors\PageflowerBundle\Pageflow;

use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Repository\RepositoryInterface;

class PageflowRepository implements RepositoryInterface
{
    /**
     * @var array
     */
    private $pageflows = array();

    /**
     * {@inheritDoc}
     */
    public function add(EntityInterface $entity)
    {
        assert($entity instanceof Pageflow);

        $this->pageflows[$entity->getPageflowId()] = $entity;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->pageflows;
    }

    /**
     * @param string $pageflowId
     *
     * @return Pageflow
     */
    public function findByPageflowId($pageflowId)
    {
        if (array_key_exists($pageflowId, $this->pageflows)) {
            return $this->pageflows[$pageflowId];
        } else {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(EntityInterface $entity)
    {
        assert($entity instanceof Pageflow);

        if (array_key_exists($entity->getPageflowId(), $this->pageflows)) {
            unset($this->pageflows[$entity->getPageflowId()]);
        }
    }
}
