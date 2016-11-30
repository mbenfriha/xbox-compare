<?php
include('simple_html_dom.php');
include('function.php');

/*$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN'])
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});*/

$app->get('/games', function ($request, $response, $args) {
    $sth = $this->db->prepare("SELECT * FROM game ORDER BY name");
    $sth->execute();
    $games = $sth->fetchAll();
    return $this->response->withJson($games);
});

$app->get('/games/last', function($request, $response, $arg){
    $sth = $this->db->prepare("SELECT * FROM game INNER JOIN
                              (SELECT * FROM price ORDER BY value_converter) p
                              ON p.game_id=game.id GROUP BY game.id ORDER BY created_at DESC");
    $sth->execute();
    $games = $sth->fetchAll();
    return $this->response->withJson($games);
});

$app->get('/game/[{id}]', function ($request, $response, $args) {

    $sth = $this->db->prepare("SELECT * FROM game WHERE id_game=:id");
    $sth->bindValue("id", $args['id']);
    $sth->execute();
    $game = $sth->fetchObject();

    $sth2 = $this->db->prepare("SELECT value, value_converter, currency_name, name FROM price INNER JOIN country on country.id = price.country_id where game_id = :id");
    $sth2->bindValue("id", $game->id);
    $sth2->execute();
    $prices = $sth2->fetchAll();

    return $this->response->withJson(["game" => $game, "prices" => $prices]);
});

$app->post('/game/parse', function ($request)
{
    $country = [
        'ZAR' => 1,
        'ARS' => 2,
        'BRL' => 3,
        'CAD' => 4,
        'COP' => 5,
        'EUR' => 6,
        'HKD' => 7,
        'INR' => 8,
        'JPY' => 9,
        'HUF' => 10,
        'MXN' => 11,
        'RUB' => 12,
        'SGD' => 13,
        'TWD' => 14,
        'USD' => 15
    ];

    $game = getGame(urldecode($request->getParsedBody()['link']));

    $sth = $this->db->prepare('INSERT INTO game(id_game, game_slug, name, thumb, background, created_at, updated_at)
                                VALUES (:id_game, :game_slug, :name, :thumb, :background, :created_at, :updated_at)');

    try {
        $sth->execute($game['game']);
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            return $this->response->withJson(["status" => 409, "error"=>true, "message" => "Ce jeu existe déjà"]);
        }
        echo $e;
        return $this->response->withJson(["status" => 409, "error"=>true, "message" => "problème lors de l'ajout"]);

    }
    $id = $this->db->lastInsertId();

    $sth2 = $this->db->prepare("INSERT INTO price(country_id, game_id, value, value_converter) VALUES (:country_id, :game_id, :value, :value_converter)");


    foreach($game['prices'] as $k => $v) {
        $sth2->bindValue(':country_id', $k, PDO::PARAM_INT);
        $sth2->bindValue(':game_id', $id, PDO::PARAM_INT);
        $sth2->bindValue(':value', $v, PDO::PARAM_STR);
        $sth2->bindValue(':value_converter', converCurrency(array_search($k, $country), 'EUR', $v), PDO::PARAM_STR);
        $sth2->execute();
    }

    return $this->response->withJson(["message" => "Jeu ajouté avec succès !"]);
});

$app->post('/game', function ($request, $response) {

    $input = $request->getParsedBody();

    $sth = $this->db->prepare("SELECT id FROM game WHERE id_game = ?");
    $sth->execute(array(end(explode('/', $input['link']))));

    if($sth->fetch())
        return $this->response->withStatus(409)->withJson(["message" => "Ce jeu existe déjà"]);

    curl_post_async('http://xbox:8889/game/parse', $input);

    return $this->response->withJson(["message" => "ajout du jeux, veuillez patienter"]);
});

$app->put('/game/[{id}]', function ($request, $response, $args) {
    $input = $request->getParsedBody();

    $game = updateGame($input['link'], $args['id']);
    $sql = "UPDATE game SET price_ZA=:price_ZA, price_AR=:price_AR, price_BR=:price_BR, price_CA=:price_CA,
                            price_CO=:price_CO, price_FR=:price_FR, price_HK=:price_HK, price_IN=:price_IN,
                            price_JP=:price_JP, price_HU=:price_HU, price_MX=:price_MX, price_RU=:price_RU,
                            price_SG=:price_SG, price_TW=:price_TW, price_US=:price_US, updated_at=:updated_at
                            WHERE game_id=:id";
    $sth = $this->db->prepare($sql);
    try {
        $sth->execute($game);
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            return $this->response->withStatus(409)->withJson(["message" => "Impossible de mettre à jours les prix"]);
        }
    }
    return $this->response->withJson(["message" => "Les prix ont bien été mis à jour !"]);
});
