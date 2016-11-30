<?php
function curl_post_async($url, $params)
{
    foreach ($params as $key => &$val)
    {
        if (is_array($val))
            $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts = parse_url($url);

    $fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string))
        $out.= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}

function getGame($link) {
    $country = [
        'en-ZA' => 1,
        'es-AR' => 2,
        'pt-BR' => 3,
        'fr-CA' => 4,
        'es-CO' => 5,
        'fr-FR' => 6,
        'en-HK' => 7,
        'en-IN' => 8,
        'ja-JP' => 9,
        'hu-HU' => 10,
        'es-MX' => 11,
        'ru-RU' => 12,
        'en-SG' => 13,
        'zh-TW' => 14,
        'en-US' => 15
    ];
    $context = stream_context_create([
        'http' => [
            'method'        => "GET",
            'user_agent'    => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36'
        ]
    ]);
    $price = [];

    file_put_contents("test.txt", "mdrr1");
    if(!$link || count(explode('/', $link)) !== 8 || !str_get_html(file_get_contents($link, false, $context))) {
        var_dump('ok1');
        return false;
    }
    file_put_contents("test.txt", "mdrr2");

    $html = str_get_html(file_get_contents($link, false, $context));
    file_put_contents("test.txt", $html);

    foreach($html->find('.srv_itemDetails') as $element)

        $back = explode('(', $element->find('.context-image-cover', 0)->getAttribute('style'));

      if(!uploadImage($element->find('.srv_appHeaderBoxArt img', 0)->src, array_pop(explode('/', $link)))) {
          var_dump('ok2');
          return false;
      }

      if(!uploadImage($back[1], 'background-' . array_pop(explode('/', $link)))) {
          var_dump('ok3');
          return false;
      }

        $game = ([
            ':id_game' => array_pop(explode('/', $link)),
            ':game_slug' => explode('/', $link)[count(explode('/', $link)) - 2],
            ':name' => $element->find('.srv_title', 0)->plaintext,
            ':thumb' => htmlspecialchars_decode($element->find('.srv_appHeaderBoxArt img', 0)->src),
            ':background' => htmlspecialchars_decode($back[1])
        ]);

    $linkEx = explode('/', $link);

    foreach($country as $c => $value) {
        $linkEx[3] = $c;
        $html = str_get_html(file_get_contents(implode('/', $linkEx), false, $context));
        $price[$value] = $html->find('.srv_microdata meta', 0)->getAttribute('content');
    }
    $game[':created_at'] = date("Y-m-d H:i:s");
    $game[':updated_at'] = date("Y-m-d H:i:s");

    return ['game' => $game, 'prices' => $price];
}

function uploadImage($thumb, $name){
    global $app;
    $link = 'https:' . rtrim(trim(htmlspecialchars_decode($thumb), "https:"), "); ");

    if($file = file_get_contents($link)) {

        file_put_contents('public/upload/game/'. $name . '.jpeg', $file);
       // $fp = fopen('./hos'. $name . '.jpeg', "w");
       // fwrite($fp, file_get_contents($link));
       // fclose($fp);
        return true;
    }
    var_dump(rtrim('https:'.$thumb, "); "));
    return false;
}

function updateGame($link, $id) {
    $country = [
        'en-ZA',
        'es-AR',
        'pt-BR',
        'fr-CA',
        'es-CO',
        'fr-FR',
        'en-HK',
        'en-IN',
        'ja-JP',
        'hu-HU',
        'es-MX',
        'ru-RU',
        'en-SG',
        'zh-TW',
        'en-US'
    ];
    $context = stream_context_create([
        'http' => [
            'method'        => "GET",
            'user_agent'    => $_SERVER['HTTP_USER_AGENT']
        ]
    ]);

    if(!$link || count(explode('/', $link)) !== 8 || !str_get_html(file_get_contents($link, false, $context))) {
        return false;
    }

    $html = str_get_html(file_get_contents($link, false, $context));
    foreach($html->find('.srv_itemDetails') as $element)

    $game = [':id' => $id];
    $linkEx = explode('/', $link);

    foreach($country as $c) {
        $linkEx[3] = $c;
        $html = str_get_html(file_get_contents(implode('/', $linkEx), false, $context));
        $game[':price_' .explode('-', $c)[1]] = $html->find('.srv_microdata meta', 0)->getAttribute('content');
    }
    $game[':updated_at'] = date("Y-m-d H:i:s");

    return $game;
}

function converCurrency($from,$to,$amount){

    if ($from == "EUR")
        return $amount;

    $url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to";
    $request = curl_init();
    $timeOut = 0;
    curl_setopt ($request, CURLOPT_URL, $url);
    curl_setopt ($request, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($request, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
    curl_setopt ($request, CURLOPT_CONNECTTIMEOUT, $timeOut);
    $response = curl_exec($request);
    curl_close($request);
    preg_match('#\<span class=bld\>(.+?)\<\/span\>#s', $response, $finalData);
    return round(substr($finalData[1], 0, strrpos($finalData[1], ' ')), 2);
}