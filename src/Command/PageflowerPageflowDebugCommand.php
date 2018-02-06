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

namespace PHPMentors\PageflowerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PageflowerPageflowDebugCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('pageflower:pageflow:debug')
            ->setDescription('Displays current pageflows for an application')
            ->setHelp(
'The <info>%command.name%</info> displays the configured pageflows:'.PHP_EOL.
PHP_EOL.
'  <info>php %command.full_name%</info>'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pageflows = $this->getContainer()->get('phpmentors_pageflower.pageflow_repository')->findAll();

        $output->writeln($this->getHelper('formatter')->formatSection('pageflower', 'Pageflows'));

        $pageflowIdMaxLength = strlen('pageflowId');

        foreach ($pageflows as $pageflowId => $pageflow) {
            $pageflowIdMaxLength = max($pageflowIdMaxLength, strlen($pageflowId));
        }

        $format = '%-'.$pageflowIdMaxLength.'s';
        $formatHeader = '%-'.($pageflowIdMaxLength + 19).'s';
        $output->writeln(sprintf($formatHeader, '<comment>Pageflow Id</comment>'));

        foreach ($pageflows as $pageflowId => $pageflow) {
            $output->writeln(sprintf($format, $pageflowId), OutputInterface::OUTPUT_RAW);
        }
    }
}
