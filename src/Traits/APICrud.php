<?php

namespace Phaney\ApiCrud\Traits;

use App\Exceptions\PermissionDeniedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait APICrud
{
    protected const OPERATIONS = ['List', 'Create', 'Edit', 'Destroy', 'Show'];

    public static function setupRoute($prefixRouteName)
    {
        foreach (self::OPERATIONS as $operation) {
            $operationSetup = 'setup' . $operation . 'Routes';

            if (method_exists(self::class, $operationSetup)) {
                self::{$operationSetup}($prefixRouteName);
            }
        }
    }

    public function verifyPermissions(array $permissions)
    {
        /** @var User $user */
        $user = auth()->user();
        $user->syncRoles('editor');
        foreach ($permissions as $permission) {
            if (!$user->can($permission)) {
                throw new PermissionDeniedException();
            }
        }
    }

    public function successResponse($response = null): JsonResponse
    {
        return response()->json(['data' => $response], Response::HTTP_OK);
    }

    public function errorResponse($data, $statusCode = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return response()->json([
            'data' => $data
        ], $statusCode);
    }
}
