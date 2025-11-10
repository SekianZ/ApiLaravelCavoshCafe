<?php

namespace App\Exceptions\Product;

class ProductNotFoundException extends ProductException
{
    public function __construct(string $message = "Producto no encontrado", int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
