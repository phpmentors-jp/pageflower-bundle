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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @since Class available since Release 1.1.0
 */
class ConversationalControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $_SERVER['KERNEL_DIR'] = __DIR__.'/app';
        include_once __DIR__.'/app/AppKernel.php';
        $_SERVER['KERNEL_CLASS'] = 'AppKernel';
        $this->removeCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->removeCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = array())
    {
        $kernel = parent::createKernel($options);

        if (array_key_exists('config', $options)) {
            $kernel->setConfig($options['config']);
        }

        return $kernel;
    }

    private function removeCacheDir()
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($_SERVER['KERNEL_DIR'].'/cache/test');
    }

    /**
     * @test
     */
    public function converseFromBeginningToEnd()
    {
        $client = static::createClient(array('config' => function (ContainerBuilder $container) {
            $container->loadFromExtension('phpmentors_pageflower', array('conversation' => array('end_on_next_step_of_end_page' => true)));
        }));

        $client->request('GET', '/user/registration/');

        $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
        $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('input'));

        $form = $client->getCrawler()->selectButton('user_registration[next]')->form();
        $form['user_registration[firstName]'] = 'Atsuhiro';
        $form['user_registration[lastName]'] = 'Kubo';

        $client->submit($form);

        $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(302));

        $client->request('GET', $client->getResponse()->headers->get('Location'));

        $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
        $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('confirmation'));
        $this->assertThat($client->getCrawler()->filterXpath('//*[@id="first_name"]')->text(), $this->stringContains('Atsuhiro'));
        $this->assertThat($client->getCrawler()->filterXpath('//*[@id="last_name"]')->text(), $this->stringContains('Kubo'));

        $client->submit($client->getCrawler()->selectButton('form[next]')->form());

        $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(302));

        $client->request('GET', $client->getResponse()->headers->get('Location'));

        $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
        $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('success'));
    }
}
