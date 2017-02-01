<?php
namespace App\Services\Implementations;

use App\Alert;
use App\Country;
use App\Game;
use App\Price;
use App\Services\Interfaces\GameService;
use App\User;
use App\Gold;
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
    public function listGameDiscount()
    {
        return Game::with(['prices' => function ($query) {
            $query->orderBy('euro_value', 'asc');
        }])->where('discount', 'LIKE', 1)->where('type', 'LIKE', 'Games')->orderBy('name', 'asc')->get();
    }

    public function addonList($gameid)
    {
        return Game::with(['prices' => function ($query) {
            $query->orderBy('euro_value', 'asc');
        }])->where('addon_id', 'LIKE', $gameid)->get();
    }

    public function listerGold()
    {
        return Gold::with('game')->get();
    }

    public function lastLister()
    {
        return Game::with(['prices' => function ($query) {
            $query->orderBy('euro_value', 'asc')->where('euro_value', '>', 0);
        }])->where('type', 'LIKE', 'Games')->orderBy('created_at', 'desc')->limit(4)->get();
    }

    public function ajouter($msLink, $gameID = false)
    {

        $a = explode('/', $msLink);
        $b = Game::findOrFail(end($a));
        if($b)
            return false;

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

        $url = 'http://137.74.168.74:10123/?link=' . $msLink;
        $obj = json_decode(file_get_contents($url), true);

        $msLinkData = explode('/', $msLink);

        if(!is_dir('img/'. end($msLinkData))) {
            mkdir('img/'. end($msLinkData));
            chmod('img/'. end($msLinkData), 0755);
        }

        if($obj['background'][0] == "/"){
            file_put_contents('img/' . end($msLinkData) . '/background.jpeg', file_get_contents(explode('?', 'https:' . $obj['background'])[0]));
            file_put_contents('img/' . end($msLinkData) . '/cover.jpeg', file_get_contents(explode('?', 'https:' . $obj['cover'])[0]));
        }

        else{
            file_put_contents('img/' . end($msLinkData) . '/background.jpeg', file_get_contents($obj['background']));
            file_put_contents('img/' . end($msLinkData) . '/cover.jpeg', file_get_contents($obj['cover']));
        }

        chmod('img/' . end($msLinkData) . '/background.jpeg', 0755);
        chmod('img/' . end($msLinkData) . '/cover.jpeg', 0755);

        $idGame = false;
        if($obj['type'] == "Games")
            $idGame = end($msLinkData);

        $addonId = false;
        if($obj['type'] == 'AddOns')
            $addonId = $gameID;

        $game = Game::create([
            'id'            => end($msLinkData),
            'name'          => $obj['name'],
            'slug'          => $msLinkData[count($msLinkData) - 2],
            'discount'      => $obj['discount'],
            'type'          => $obj['type'],
            'description'   => $obj['description'],
            'studio'        => $obj['studio'],
            'addon_id'      => $addonId,
        ]);

        if (count($obj['addons']) > 0)
        {
            foreach($obj['addons'] as $addon) {
                $this->ajouter('https://www.microsoft.com' . $addon, $idGame);
            }
        }

        foreach(Country::all() as $country)
        {
            $msLinkData[3] = $country->lang;

            $value = 0;
            $value_discount = 0;
            $euro_value_discount = 0;
            $discount = false;

            $url = 'http://137.74.168.74:10123/?link=' . implode('/', $msLinkData);
            $obj = json_decode(file_get_contents($url), true);

            $value = $obj['value'];
            $value = floatval(str_replace(',', '.',  $value));

            if ($country->currency_name == $defaultCurrency || $value == 0)
                $converted_value = $value;
            else
            {
                $crawler2 = Goutte::request('GET', "http://www.google.com/finance/converter?a={$value}&from={$country->currency_name}&to={$defaultCurrency}");
                $converted_value = round($crawler2->filter(".bld")->first()->text(), 2);
            }

            if ($obj['discount'] && $obj['value_discount'] != "") {
                $value_discount = $obj['value_discount'];

                if ($country->currency_name == $defaultCurrency || $value_discount == 0)
                    $euro_value_discount = $value_discount;
                else
                {
                    $crawler2 = Goutte::request('GET', "http://www.google.com/finance/converter?a={$value_discount}&from={$country->currency_name}&to={$defaultCurrency}");
                    $euro_value_discount = round($crawler2->filter(".bld")->first()->text(), 2);
                }
                $discount = true;
            }

            $game->prices()->create([
                'value'                 => $value,
                'euro_value'            => $converted_value,
                'country_id'            => $country->id,
                'value_discount'        => $value_discount,
                'euro_value_discount'   => $euro_value_discount,
                'discount'  => $discount
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


            $value = 0;
            $value_discount = 0;
            $euro_value_discount = 0;
            $discount = false;

            $url = 'http://137.74.168.74:10123/?link=' . implode('/', $msLinkData);
            $obj = json_decode(file_get_contents($url), true);

            $value = $obj['value'];
            $value = floatval(str_replace(',', '.',  $value));

            if ($country->currency_name == $defaultCurrency || $value == 0)
                $converted_value = $value;
            else
            {
                $crawler2 = Goutte::request('GET', "http://www.google.com/finance/converter?a={$value}&from={$country->currency_name}&to={$defaultCurrency}");
                $converted_value = round($crawler2->filter(".bld")->first()->text(), 2);
            }

            if ($obj['discount'] && $obj['value_discount'] != "") {
                $value_discount = $obj['value_discount'];

                if ($country->currency_name == $defaultCurrency || $value_discount == 0)
                    $euro_value_discount = $value_discount;
                else
                {
                    $crawler2 = Goutte::request('GET', "http://www.google.com/finance/converter?a={$value_discount}&from={$country->currency_name}&to={$defaultCurrency}");
                    $euro_value_discount = round($crawler2->filter(".bld")->first()->text(), 2);
                }
                $discount = true;
            }

            $price = Price::where([
                ['country_id', "=", $country->id],
                ['game_id', "=", $game_id]
            ])->first();
            $price->value = $value;
            $price->euro_value = $converted_value;
            $price->value_discount = $value_discount;
            $price->euro_value_discount = $euro_value_discount;
            $price->save();
        }


        foreach(Alert::where('game_id', "=",$game_id)->where('send', "=", false)->get() as $user)
        {
            $price = Price::where('game_id', $game_id)->where('euro_value', '>', 0)->min('euro_value');
            $game = Game::find($game_id);

            if($price < $user->price) {

                Mail::raw('
	Salut,
	Le jeu que tu surveillais est dessous du prix que tu souhaitais, profites-en vite en cliquant ici -> http://xbox-store-compare.com/game/'. $game->id
                    , function ($m) use (&$user, &$game) {
                        $m->from('contact@xbox-store-compare.com', 'Xbox Store Compare');

                        $m->to($user->email, "Gamer")->subject('Bonne Nouvelle ! '.$game->name . ' est moin chère');
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

    public function filter($page, $order, $asc, $price = 1000, $type = 'Games')
    {

        $limit = 12;
        $page = $page - 1;
        $offset = $page * $limit;

        $total = Game::whereHas('prices', function($q) use (&$price, &$type)
        {
            $q->where('games_prices.euro_value', '<', $price)->where('games_prices.euro_value', '>', 1.00)->where('type', 'LIKE', $type);

        })->count();


        if($offset >= $total){
            $offset = $total - $limit;
        }

        $game = Game::with(['prices' => function ($query) {
            $query->where('games_prices.euro_value', '>', 0)->orderBy('euro_value', 'asc');
        }])->whereHas('prices', function($q) use (&$price)
        {
            $q->where('games_prices.euro_value', '<', $price)->where('games_prices.euro_value', '>', 1.00);
        })->where('type', 'LIKE', $type)->take($limit)->offset($offset)->orderBy($order, $asc)->get();

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

    public function addGold($id)
    {
        $game = Game::find($id);
        $game->gold()->create([]);

        return $game;
    }

}