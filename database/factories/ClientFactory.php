<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Model;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->randomElement(['groenezang', 'meteropnull', 'greeni', 'neutraallicht', 'geenplastic', 'groenenergieentech']);
    return [
        'name' => $name,
        'short' => \Illuminate\Support\Str::slug($name),
    ];
    }
}
