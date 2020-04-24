<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Endpoint;

use IteratorAggregate;
use Mvo\MeLike\Entity\Like;

class EndpointRegistry
{
    /** @var EndpointInterface[] */
    private array $handlers;

    public function __construct(IteratorAggregate $taggedServices)
    {
        $this->handlers = iterator_to_array($taggedServices->getIterator());
    }

    public function isValidEndpoint(string $endpoint): bool
    {
        [$domain, $id] = $this->parseName($endpoint);

        // filter answers
        $answers = array_filter(
            array_map(
                fn (EndpointInterface $handler) => $handler->handle($domain, $id),
                $this->handlers
            )
        );

        // fallback if there are no votes
        if (empty($answers)) {
            return false;
        }

        // return false if any value is false
        return (bool) array_product($answers);
    }

    public function getContext(Like $like): array
    {
        [$domain, $id] = $this->parseName($like->getEndpoint());

        $contexts = array_filter(
            array_map(
                static fn (EndpointInterface $handler) => (true === $handler->handle($domain, $id))
                    ? $handler->addContext($domain, $id, $like) : null,
                $this->handlers
            )
        );

        if (empty($contexts)) {
            return [];
        }

        return array_merge(...$contexts);
    }

    private function parseName(string $endpoint): array
    {
        // match '<name>.<id>'
        if (preg_match('/^([a-zA-Z0-9]+)\.(\d+)$/', $endpoint, $matches)) {
            return [$matches[1], (int) $matches[2]];
        }

        return [$endpoint, null];
    }
}
