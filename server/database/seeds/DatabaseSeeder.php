<?php

use App\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->delete();
        DB::table('games_prices')->delete();
        DB::table('games')->delete();

        $countries = [
            ['name' => 'south-africa',		'currency_name' => 'ZAR', 'lang' => 'en-ZA'],
            ['name' => 'argentina',		    'currency_name' => 'ARS', 'lang' => 'es-AR'],
            ['name' => 'brasil',		    'currency_name' => 'BRL', 'lang' => 'pt-BR'],
            ['name' => 'canada',		    'currency_name' => 'CAD', 'lang' => 'fr-CA'],
            ['name' => 'colombia',		    'currency_name' => 'COP', 'lang' => 'es-CO'],
            ['name' => 'france',		    'currency_name' => 'EUR', 'lang' => 'fr-FR'],
            ['name' => 'honk-kong',		    'currency_name' => 'HKD', 'lang' => 'en-HK'],
            ['name' => 'india',		        'currency_name' => 'INR', 'lang' => 'en-IN'],
            ['name' => 'japan',		        'currency_name' => 'JPY', 'lang' => 'ja-JP'],
            ['name' => 'Magyarorszag',		'currency_name' => 'HUF', 'lang' => 'hu-HU'],
            ['name' => 'mexico',		    'currency_name' => 'MXN', 'lang' => 'es-MX'],
            ['name' => 'russia',		    'currency_name' => 'RUB', 'lang' => 'ru-RU'],
            ['name' => 'singapore',		    'currency_name' => 'SGD', 'lang' => 'en-SG'],
            ['name' => 'taiwan',		    'currency_name' => 'TWD', 'lang' => 'zh-TW'],
            ['name' => 'usa',		        'currency_name' => 'USD', 'lang' => 'en-US']
        ];

        foreach($countries as $country)
            Country::create($country);
    }
}

