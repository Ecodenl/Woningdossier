<?php

namespace Database\Factories;

use App\Enums\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CooperationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->company;
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'country' => Country::COUNTRY_NL,
            'website_url' => $this->faker->url,
            'cooperation_email' => $this->faker->email,
        ];
    }
}
