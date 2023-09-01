<?php

namespace Phaney\ApiCrud\Operations;

use Illuminate\Support\Facades\Route;

trait ShowOperation
{
  /**
   * Define which routes are needed for this operation.
   */
  public static function setupShowRoutes(string $prefixRouteName): void
  {
    Route::get('/show/{id}', [self::class, 'show'])->name($prefixRouteName . 'show');
  }

  public function show(int $id)
  {
    $this->verifyPermissions($$this->getShowPermissions());
    $entry = $this->model::findOrFail($id);
    $this->setUpShowOperationColumns();

    return $this->successResponse($this->transformColumn($entry));
  }

  /**
   * 
   */
  public function setUpShowOperationColumns(): void
  {
    // for override
  }

  public function getShowPermission()
  {
    return [];
  }
}
