<?php
namespace App\Services\Interfaces;

interface GameService
{
    public function lister();
    public function ajouter($msMsLink);
    public function modifierPrix($game_id);
}