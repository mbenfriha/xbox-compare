<?php

namespace App\Http\Controllers;

use App\Game;
use App\Services\Implementations\GameServiceImpl;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class GameController extends Controller
{
    public function getLastList()
    {

        return response()->json(app(GameServiceImpl::class)->lastLister());
    }

    public function filter()
    {
        Input::get('price') ? $price =Input::get('price') : $price = 1000;
        return response()->json(app(GameServiceImpl::class)->filter(Input::get('page'), Input::get('order'), Input::get('asc'), $price));
    }

    public function getList()
    {
        return response()->json(app(GameServiceImpl::class)->lister());
    }

    public function getGame($id)
    {
        return response()->json(app(GameServiceImpl::class)->single($id));
    }

    public function find(Request $request)
    {
        return response()->json(app(GameServiceImpl::class)->find($request->name));
    }

    public function post(Request $request)
    {

        try {
            $a = explode('/', $request->link);
            $game = Game::findOrFail(end($a));
            return response()->json(['message' => 'exist']);
        }catch (ModelNotFoundException $e)
        {
         return response()->json(['message' => app(GameServiceImpl::class)->ajouter($request->link)]);
  //         $this->action_post_async("http://xbox:8889/game/parse", $request->only('link'));
            return response()->json(['message' => 'wait_post']);
        }
    }

    public function refresh($id)
    {
        $game = Game::findOrFail($id);
        $diff  = abs(time() - strtotime($game->update_at));
        $min = floor( ($diff - $diff % 60) / 60 ) % 60;

        if ($min < 0){
            return response()->json(['message' => 'time']);
        }


      return response()->json(app(GameServiceImpl::class)->modifierPrix($id));
        //$this->action_post_async("http://xbox:8889/game/refresh", ["id" => $id]);
        return response()->json(['message' => 'wait_refresh']);


    }

    public function alert(Request $request)
    {
        return response()->json(app(GameServiceImpl::class)->alertUser($request->email, $request->game_id, $request->price));
    }

    public function post_async(Request $request)
    {
        return response()->json(['message' => app(GameServiceImpl::class)->ajouter($request->link)]);
    }

    public function refresh_async(Request $request)
    {
        return response()->json(app(GameServiceImpl::class)->modifierPrix($request->id));
    }

    private function action_post_async($url, $params)
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
}
