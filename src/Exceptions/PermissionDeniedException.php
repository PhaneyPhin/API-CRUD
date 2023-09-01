<?php

namespace Phaney\ApiCrud\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionDeniedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Permission denied', Response::HTTP_FORBIDDEN);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage() 
          ], $this->getCode());
    }
}
