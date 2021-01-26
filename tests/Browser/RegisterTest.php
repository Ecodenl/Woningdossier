<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Factory;
use Faker\Generator;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    protected function setUp(): void
    {
        return parent::setUp();
        User::where('email', 'test@example.org')->delete();
    }

    public function getLocalizedFaker()
    {
        $faker = Factory::create('nl_NL');

        return $faker;
    }

    public function getRandomHouseNumberExtension(Generator $faker)
    {
        $additions = ['', ' ', 'BOVEN', 'BENEDEN', 'ONDER', 'HS', 'bis', 'ZW', 'RD', '-12'];

        return $additions[mt_rand(0, count($additions) - 1)];
    }

    /**
     * For now, we go with faker. We have to eliminate this when the pico data
     * is present.
     *
     * @return void
     */
    public function testRegistration()
    {
        $faker = $this->getLocalizedFaker();

        $this->browse(function (Browser $browser) use ($faker) {
            $browser->visit('http://hoom.woondossier.vm')
                ->clickLink('Registreren')
                ->assertPathIs('/register')
                ->value('#email', 'test@example.org')
                ->value('#first_name', $faker->firstName)
                ->value('#last_name', $faker->lastName)
                ->value('#postal_code', $faker->postcode)
                ->value('#number', $faker->numberBetween(0, 10000))
                ->value('#house_number_extension', $this->getRandomHouseNumberExtension($faker))
                ->value('#street', $faker->streetName)
                ->value('#city', $faker->city)
                ->value('#phone_number', 1 == mt_rand(0, 1) ? $faker->phoneNumber : '')
                ->value('#password', 'secret')
                ->value('#password-confirm', 'secret')
                ->click('button[type="submit"]') //Click the submit button on the page
                ->assertSee('Bedankt')
            ;
        });
    }

    protected function tearDown(): void
    {
        User::where('email', 'test@example.org')->delete();
        parent::tearDown();
    }
}
