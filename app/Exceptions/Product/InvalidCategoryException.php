<?php

namespace App\Exceptions\Product;

class InvalidCategoryException extends ProductException
{
    public function __construct(string $message = "La categoría especificada no existe", int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
