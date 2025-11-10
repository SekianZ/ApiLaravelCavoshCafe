<?php

namespace App\Exceptions\Product;

class ProductDeleteException extends ProductException
{
    public function __construct(string $message = "Error al eliminar el producto", int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
