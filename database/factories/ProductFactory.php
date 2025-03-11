<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        $department = Department::factory()->create();

        return [
            'department_id' => $department->id,
            'name' => $name,
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(10, 100),
            'min_stock_threshold' => 10,
            'slug' => Str::slug($name),
            'category' => $this->faker->word(),
            'is_promotional' => $this->faker->boolean(30)
        ];
    }
}