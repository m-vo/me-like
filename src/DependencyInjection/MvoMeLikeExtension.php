<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\DependencyInjection;

use Mvo\MeLike\Endpoint\EndpointInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MvoMeLikeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        // load services
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');

        // process config
        $container->setParameter('mvo_me_like.user_token', $config['user_token']);
        $container->setParameter('mvo_me_like.confirm_token', $config['confirm_token']);

        $container->setParameter('mvo_me_like.notification.email_from', $config['notification']['email_from'] ?? null);

        // setup autoconfiguration
        $container
            ->registerForAutoconfiguration(EndpointInterface::class)
            ->addTag('mvo_me_like.endpoint');
    }
}
