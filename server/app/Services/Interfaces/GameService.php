<?php


interface GameService
{
    public function lister();
    public function ajouter(array $information);
    public function modifierPrix($game_id);

}