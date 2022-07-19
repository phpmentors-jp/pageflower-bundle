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

namespace PHPMentors\PageflowerBundle;

use PHPMentors\PageflowerBundle\DependencyInjection\Compiler\GeneratePageflowDefinitionsPass;
use PHPMentors\PageflowerBundle\DependencyInjection\Compiler\ReplaceAppVariableDefinitionPass;
use PHPMentors\PageflowerBundle\DependencyInjection\Compiler\ReplaceSessionDefinitionPass;
use PHPMentors\PageflowerBundle\DependencyInjection\Compiler\ReplaceTemplatingGlobalsDefinitionPass;
use PHPMentors\PageflowerBundle\DependencyInjection\PHPMentorsPageflowerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PHPMentorsPageflowerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GeneratePageflowDefinitionsPass());
        $container->addCompilerPass(new ReplaceTemplatingGlobalsDefinitionPass());
        $container->addCompilerPass(new ReplaceAppVariableDefinitionPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if ($this->extension === null) {
            $this->extension = new PHPMentorsPageflowerExtension();
        }

        return $this->extension;
    }
}
