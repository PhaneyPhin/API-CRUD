<?php

namespace Phaney\ApiCrud\Operations;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

trait UpdateOperation
{
    protected int $id;

    /**
     * Define which routes are needed for this operation.
     */
    public static function setupEditRoutes(string $prefixRouteName)
    {
        Route::post('/update/{id}', [self::class, 'edit'])->name($prefixRouteName . 'store');
    }

    public function edit(int $id, Request $request)
    {
        $this->id = $id;
       try {
        $model = $this->model::findOrFail($id);
        $validated = $request->validate($this->getUpdateValidationRequest());
        $model = $this->update($model, $validated);

        return $this->successResponse($model, 'success full');
       } catch(ValidationException $e) {
            return $this->errorResponse($e->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
       }
    }

    public function getUpdateValidationRequest() : array
    {
        return [
            
        ];
    }

    public function update($model, array $data)
    {
        $model->fill($data);
        $model->save();
    }
}
