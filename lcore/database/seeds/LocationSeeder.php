<?php

use Illuminate\Database\Seeder;
use App\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            [
                'name' => 'Jaipur, India',
                'status' => 'active',
            ],
            [
                'name' => 'New York, USA',
                'status' => 'active',
            ],
        ];
        foreach ($locations as $key => $location) {
            Location::create($location);
        }
    }
}
