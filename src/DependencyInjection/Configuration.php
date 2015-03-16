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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('phpmentors_pageflower')
            ->children()
                ->arrayNode('conversation')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('request_parameter_name')
                            ->defaultValue('CONVERSATION_ID')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('session_storage_name')
                            ->defaultValue('_pageflower_conversations')
                            ->cannotBeEmpty()
                        ->end()
                        ->booleanNode('end_on_next_step_of_end_page')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
