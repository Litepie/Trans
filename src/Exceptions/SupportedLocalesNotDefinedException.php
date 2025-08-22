<?php

declare(strict_types=1);

namespace Litepie\Trans\Exceptions;

use Exception;

/**
 * Exception thrown when supported locales are not properly defined.
 *
 * @package Litepie\Trans\Exceptions
 */
class SupportedLocalesNotDefinedException extends Exception
{
    /**
     * Create a new SupportedLocalesNotDefinedException instance.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = 'Supported locales are not defined in configuration.', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
