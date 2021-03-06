<?php
namespace App\Services\Interfaces;

interface GameService
{
    public function lister();
    public function listGameDiscount();
    public function addonList($gameid);
    public function listerGold();
    public function ajouter($msLink);
    public function modifierPrix($game_id);
    public function filter($page, $order, $asc, $price = 1000);
    public function find($name);
    public function single($game_id);
    public function lastLister();
    public function alertUser($email, $game_id, $price);
    public function addGold($id);
}