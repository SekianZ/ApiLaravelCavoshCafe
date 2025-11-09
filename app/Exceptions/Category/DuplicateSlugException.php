<?php

namespace App\Exceptions\Category;

use Exception;

class DuplicateSlugException extends Exception
{
    public function __construct(string $slug = "", int $code = 409, ?\Throwable $previous = null)
    {
        $message = $slug ? "El slug '{$slug}' ya existe" : "El slug ya existe";
        parent::__construct($message, $code, $previous);
    }
}
