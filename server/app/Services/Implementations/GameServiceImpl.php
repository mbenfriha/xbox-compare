<?php
namespace App\Services\Implementations;

use App\Country;
use App\Game;
use App\Price;
use App\Services\Interfaces\GameService;
use Goutte;

class GameServiceImpl implements GameService
{
    public function lister()
    {
        return Game::with(['prices' => function ($query) {
            $query->orderBy('euro_value', 'asc');
        }])->orderBy('name', 'asc')->get();
    }

    public function lastLister()
    {
        return Game::with(['prices' => function ($query) {
            $query->orderBy('euro_value', 'asc');
        }])->orderBy('created_at', 'desc')->limit(10)->get();
    }

    public function ajouter($msLink)
    {
        $defaultCurrency = 'EUR';
        $l = (parse_url($msLink));

        if(!$msLink || count(explode('/', $msLink)) !== 8)
            return false;
        if($l['host'] !== 'www.microsoft.com')
            return false;

        // edit link to en-US

        $l2 = explode('/', $l['path']);
        $l2[1] = "en-US";
        $l2 = implode('/', $l2);
        $l['scheme'] = "http:/";
        $l['path'] = ltrim($l2, '/');
        $msLink = implode('/', $l);

        $crawler = Goutte::request('GET', $msLink);

        //'background' => explode('(', $crawler->filter('.context-image-cover')->first()->attr('style'))[1]
        //'cover' => $crawler->filter('.srv_appHeaderBoxArt img')->first()->attr('src'))

        $msLinkData = explode('/', $msLink);
        $game = Game::create([
                'id'        => end($msLinkData),
                'name'      => $crawler->filter('.srv_title')->first()->text(),
                'slug'      => $msLinkData[count($msLinkData) - 2]
            ]
        );

        foreach(Country::all() as $country)
        {
            $msLinkData[3] = $country->lang;

            $crawler = Goutte::request('GET', implode('/', $msLinkData));
            $value = $crawler->filter('.srv_microdata meta')->first()->attr('content');
            if ($country->currency_name == $defaultCurrency)
                $converted_value = $value;
            else
            {
                $crawler = Goutte::request('GET', "http://www.google.com/finance/converter?a={$value}&from={$country->currency_name}&to={$defaultCurrency}");
                $converted_value = round($crawler->filter(".bld")->first()->text(), 2);
            }

            $game->prices()->create([
                'value'         => $value,
                'euro_value'    => $converted_value,
                'country_id'    => $country->id
            ]);


        }

        return $game;
    }

    public function modifierPrix($game_id)
    {

        $defaultCurrency = 'EUR';

        $game = Game::find($game_id);
        $game->updated_at = date("Y-m-d H:i:s");
        $game->save();

        $msLink = "https://www.microsoft.com/en-US/store/p/$game->slug/$game_id";

        $msLinkData = explode('/', $msLink);

        foreach(Country::all() as $country)
        {
            $msLinkData[3] = $country->lang;

            $crawler = Goutte::request('GET', implode('/', $msLinkData));
            $value = $crawler->filter('.srv_microdata meta')->first()->attr('content');

            if ($country->currency_name == $defaultCurrency)
                $converted_value = $value;
            else
            {
                $crawler = Goutte::request('GET', "http://www.google.com/finance/converter?a={$value}&from={$country->currency_name}&to={$defaultCurrency}");
                $converted_value = round($crawler->filter(".bld")->first()->text(), 2);
            }

            $price = Price::where([
                ['country_id', "=", $country->id],
                ['game_id', "=", $game_id]
            ])->first();
            $price->value = $value;
            $price->euro_value = $converted_value;

            $price->save();


        }
        return $game;

    }

    public function single($game_id)
    {
        return Game::with('prices', 'prices.country')->findOrFail($game_id);
    }
}