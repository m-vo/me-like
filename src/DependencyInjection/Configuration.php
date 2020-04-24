<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mvo_me_like');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('user_token')
                    ->info('Name of the user token (used as fragment in the URL). Make sure there are no anchors named like this.')
                    ->cannotBeEmpty()
                    ->defaultValue('tok_u')
                ->end()
                ->scalarNode('confirm_token')
                    ->info('Name of the confirm token (used as fragment in the URL). Make sure there are no anchors named like this.')
                    ->cannotBeEmpty()
                    ->defaultValue('tok_c')
                ->end()
                ->arrayNode('notification')
                    ->children()
                        ->scalarNode('email_from')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
