import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

import { Game }                from '../game/game';
import { GameService }         from '../game/game.service';

import myGlobals = require('../globals');


@Component({
    selector: 'slider-games',
    templateUrl: 'slider.component.html',
    styleUrls: [ 'slider.component.css' ]
})
export class SliderComponent implements OnInit {
    games: Game[];

    link = "http://api.xbox-store-compare.com/img/";
    img = "/background.jpeg";
    get myGlobals() { return myGlobals; }

    constructor(
        private gameService: GameService,
        private router: Router) { }

    getGames(): void {
        this.gameService
            .getLastGames()
            .subscribe((games: Game[]) => {
                this.games = games;

                console.log(games);
            }, (err: any) => {
                console.log(err);
                this.games = null;
            }, () => console.log(''));
    }

    view(): void {

    }

    ngOnInit(): void {
        this.getGames();
    }
}