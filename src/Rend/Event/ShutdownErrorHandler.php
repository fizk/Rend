<?php
namespace Rend\Event;

class ShutdownErrorHandler
{
    public function __invoke()
    {
        if ($error = error_get_last()) {
            echo json_encode(error_get_last());
            exit;
        }
    }
}
