<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SliderFactory extends Factory
{
    protected $model = \App\Models\Slider::class;

    public function definition()
    {
        return [
            // You can use placeholder images
            'image' => 'https://via.placeholder.com/1200x400?text=Slide+' . $this->faker->unique()->numberBetween(1, 100),
            'caption' => $this->faker->sentence(5),
        ];
    }
}
