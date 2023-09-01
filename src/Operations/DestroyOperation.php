<?php

namespace Phaney\ApiCrud\Operations;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

trait DestroyOperation
{
    protected array $destroyPermissions = [];
    protected static $forceDelete = false;

    /**
     * Define which routes are needed for this operation.
     */
    public static function setupDestroyRoutes(string $prefixRouteName): void
    {
        Route::post('/destroy/{id}', [self::class, 'destroy'])->name($prefixRouteName . 'destroy');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->verifyPermissions($this->getDestroyPermissions());
        $entry = $this->model::findOrFail($id);

        if (self::$forceDelete) {
            $entry->forceDelete();
        } else {
            $entry->delete();
        }

        return $this->successResponse([], 'Data deleted');
    }

    public function getDestroyPermissions()
    {
        return [];
    }
}
