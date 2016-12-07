<?php
namespace App\Services\Implementations;

use App\Alert;
use App\Country;
use App\Game;
use App\Price;
use App\Services\Interfaces\GameService;
use App\User;
use Goutte;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

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

        $msLinkData = explode('/', $msLink);

        if(!is_dir('img/'. end($msLinkData))) {
            mkdir('img/'. end($msLinkData));
        }

        if($file = file_get_contents(explode('(', $crawler->filter('.context-image-cover')->first()->attr('style'))[1])) {
            file_put_contents('img/' . end($msLinkData) . '/background.jpeg', $file);
        }

        if($file = file_get_contents($crawler->filter('.srv_appHeaderBoxArt img')->first()->attr('src'))) {
            file_put_contents('img/'. end($msLinkData) . '/cover.jpeg', $file);
        }

        $game = Game::create([
                'id'        => end($msLinkData),
                'name'      => $crawler->filter('.srv_title')->first()->text(),
                'slug'      => $msLinkData[count($msLinkData) - 2]
            ]);

        foreach(Country::all() as $country)
        {
            $msLinkData[3] = $country->lang;

            $value = 0;

            $crawler = Goutte::request('GET', implode('/', $msLinkData));
                if($crawler->filter('.srv_microdata meta')->count()) {
                    $value = $crawler->filter('.srv_microdata meta')->first()->attr('content');
                }

            if ($country->currency_name == $defaultCurrency || $value == 0)
                $converted_value = $value;
            else
            {
                $crawler2 = Goutte::request('GET', "http://www.google.com/finance/converter?a={$value}&from={$country->currency_name}&to={$defaultCurrency}");
                $converted_value = round($crawler2->filter(".bld")->first()->text(), 2);
            }

            $game->prices()->create([
                'value'         => $value,
                'euro_value'    => $converted_value,
                'country_id'    => $country->id
            ]);


            if($crawler->filter('.price-disclaimer')->count() > 2){
                $value = $crawler->filter('.srv_microdata')->eq(3)->filter('meta')->first()->attr('content');

                if ($country->currency_name == $defaultCurrency || $value == 0)
                    $converted_value = $value;
                else
                {
                    $crawler2 = Goutte::request('GET', "http://www.google.com/finance/converter?a={$value}&from={$country->currency_name}&to={$defaultCurrency}");
                    $converted_value = round($crawler2->filter(".bld")->first()->text(), 2);
                }

                $game->prices()->create([
                    'value'         => $value,
                    'euro_value'    => $converted_value,
                    'country_id'    => $country->id,
                    'discount'      => true
                ]);
            }
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


        foreach(Alert::where('game_id', "=",$game_id)->where('send', "=", false)->get() as $user)
        {
            $price = Price::where('game_id', $game_id)->where('euro_value', '>', 0)->min('euro_value');

            if($price < $user->price) {

                Mail::raw('sayé les jeux a atteint le prix que tu souhaitais profites en vite clique ici !', function ($m) use (&$user) {
                    $m->from('contact@xbox-store-compare.com', 'Xbox Store Compare');

                    $m->to($user->email, "Gamer")->subject('**** est moin chère que ce que tu voulais !');
                });

                $alert = Alert::where([
                    ['game_id', "=", $game_id],
                    ['email', "=", $user->email]
                ])->first();
                $alert->send = true;
                $alert->save();

                var_dump('ok');
                var_dump($user->email);
            }


        }

        return $game;

    }

    public function single($game_id)
    {
        return Game::with('prices', 'prices.country')->findOrFail($game_id);
    }

    public function find($name)
    {
        return Game::with('prices', 'prices.country')
            ->where('name', 'LIKE', '%'.$name.'%')->get();
    }

    public function filter($page, $order, $asc, $price = 1000)
    {

        $limit = 5;
        $page = $page - 1;
        $offset = $page * $limit;

        $total = Game::whereHas('prices', function($q) use (&$price)
        {
            $q->where('games_prices.euro_value', '<', $price)->where('games_prices.euro_value', '>', 1.00);

        })->count();


        if($offset >= $total){
            $offset = $total - $limit;
        }

        $game = Game::with(['prices' => function ($query) {
            $query->where('games_prices.euro_value', '>', 0)->orderBy('euro_value', 'asc');
        }])->whereHas('prices', function($q) use (&$price)
        {
            $q->where('games_prices.euro_value', '<', $price)->where('games_prices.euro_value', '>', 1.00);
        })->take($limit)->offset($offset)->orderBy($order, $asc)->get();

        $nbr_page = ceil($total / $limit);
        $current_page = $page + 1;

        if ($current_page > $nbr_page)
            return ["total" => 0,"nbr_page" => 0, "current_page" => 0, "game" => "empty"];

        return ["total" => $total,"nbr_page" => $nbr_page, "current_page" => $current_page, "game" => $game];
    }

    public function alertUser($email, $game_id, $price)
    {
        User::firstOrCreate(['email' => $email]);

        $alert = Alert::firstOrCreate([
            'email'     => $email,
            'game_id'   => $game_id,
            'price'     => $price,
            'send'      => false
        ]);

        return response()->json(['message' => 'alert_add']);
    }

}