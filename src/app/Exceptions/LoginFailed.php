<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class LoginFailed extends Exception
{
    public function render()
    {
        return responseError(Response::HTTP_FORBIDDEN, '', ['unauthenticate' => $this->getMessage()]);
    }
}
