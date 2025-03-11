<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SlugService
{
    /**
     * Generate a unique slug for a model.
     *
     * @param Model $model The model to generate a slug for
     * @param string $name The name to base the slug on
     * @param string $column The column to store the slug in
     * @return string The generated unique slug
     */
    public static function createUniqueSlug(Model $model, string $name, string $column = 'slug'): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 2;

        // Check if the slug already exists in the database
        while ($model::where($column, $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}