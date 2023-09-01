<?php

namespace Phaney\ApiCrud\Operations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

trait CreateOperation
{
    /**
     * Define which routes are needed for this operation.
     */
    public static function setupCreateRoutes(string $prefixRouteName): void
    {
        Route::post('/store', [self::class, 'store'])->name($prefixRouteName . 'store');
    }

    public function store(Request $request): JsonResponse
    {
        $this->verifyPermissions($this->getStorePermissions());

        try {
            $validated = $request->validate($this->getStoreValidationRequest());
            $model = $this->save($validated);

            return $this->successResponse($model, 'successful');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getStorePermissions()
    {
        return [];
    }

    public function getStoreValidationRequest(): array
    {
        return [
            // For override    
        ];
    }

    public function save(array $data): Model
    {
        $model = new $this->model;
        $model->fill($data);
        $model->save();

        return $model;
    }
}
