<?php

declare(strict_types=1);

namespace Litepie\Trans\Exceptions;

use Exception;

/**
 * Exception thrown when an unsupported locale is used.
 *
 * @package Litepie\Trans\Exceptions
 */
class UnsupportedLocaleException extends Exception
{
    /**
     * Create a new UnsupportedLocaleException instance.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = 'Unsupported locale provided.', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
