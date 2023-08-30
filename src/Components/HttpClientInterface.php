<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Components;

interface HttpClientInterface
{
    /**
     * Returns json decoded response body
     */
    public function request(string $method, string $uri): array;
}
