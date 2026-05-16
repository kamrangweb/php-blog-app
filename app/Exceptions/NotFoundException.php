<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotFoundException extends Exception
{
    public function __construct(
        string $message = "Page not found",
        int $code = 404,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render 404 page
     */
    public function render(): void
    {
        http_response_code(404);

        require VIEWS_FOLDER . 'errors/404.view.php';

        exit;
    }
}
