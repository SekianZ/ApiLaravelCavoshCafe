<?php

namespace App\Exceptions\Product;

class ProductCreationException extends ProductException
{
    public function __construct(string $message = "Error al crear el producto", int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
