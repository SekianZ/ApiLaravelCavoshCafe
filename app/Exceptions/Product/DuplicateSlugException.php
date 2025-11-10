<?php

namespace App\Exceptions\Product;


class DuplicateSlugException extends ProductException
{
    public function __construct(string $slug = "", int $code = 409, ?\Throwable $previous = null)
    {
        $message = $slug ? "El slug '{$slug}' ya existe" : "El slug ya existe";
        parent::__construct($message, $code, $previous);
    }
}
