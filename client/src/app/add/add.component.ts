import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

import { Game }                from '../game/game';
import { GameService }         from '../game/game.service';

@Component({
    templateUrl: 'add.component.html',
    styleUrls: [ 'add.component.css' ]
})
export class AddComponent implements OnInit {
    games: Game[];

    constructor(
        private gameService: GameService,
        private router: Router) { }


    ngOnInit(): void {}
}