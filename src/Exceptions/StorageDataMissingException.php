<?php
declare(strict_types=1);

namespace Tymeshift\PhpTest\Exceptions;

final class StorageDataMissingException extends \Exception
{
    private const MESSAGE = 'Storage data not found.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 500);
    }
}
