import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

import { Game }                from '../game/game';
import { GameService }         from '../game/game.service';

@Component({
    templateUrl: 'buy.component.html',
    styleUrls: [ 'buy.component.css' ]
})
export class BuyComponent implements OnInit {
    games: Game[];

    constructor(
        private gameService: GameService,
        private router: Router) { }


    ngOnInit(): void {}
}