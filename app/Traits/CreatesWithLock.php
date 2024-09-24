<?php

namespace App\Traits;

trait CreatesWithLock
{
  public static function updateOrCreate(array $attributes, array $values = [])
  {
    return static::advisoryLock(function () use ($attributes, $values) {
      // emulate the code found in Illuminate\Database\Eloquent\Builder
      return (new static)->newQuery()->updateOrCreate($attributes, $values);
    });
  }

  public static function firstOrCreate(array $attributes, array $values = [])
  {
    return static::advisoryLock(function () use ($attributes, $values) {
      return (new static)->newQuery()->firstOrCreate($attributes, $values);
    });
  }

  /**
   * In my project, this advisoryLock method actually lives as a function on the global namespace (similar to Laravel Helpers).  
   * In that case the $lockName, and default lock duration are pased in as arguments.  
   */
  private static function advisoryLock(callable $callback)
  {
    // Lock name based on Model.
    $lockName = substr(static::class . ' *OrCreate lock', -64);

    // Lock for at most 10 seconds.  This is the MySQL >5.7.5 implementation.
    // Older MySQL versions have some weird behavior with GET_LOCK().
    // Other databases have a different implementation.
    \DB::statement("SELECT GET_LOCK('" . $lockName . "', 10)");

    $output = $callback();
    \DB::statement("SELECT RELEASE_LOCK('" . $lockName . "')");
    return $output;
  }
}