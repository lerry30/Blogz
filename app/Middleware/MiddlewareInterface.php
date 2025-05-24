<?php
namespace App\Middleware;

/**
 * Middleware Interface
 */
interface MiddlewareInterface
{
  /**
   * Handle the incoming request
   *
   * @param mixed $request
   * @return mixed
   */
  public function handle($request);
}
