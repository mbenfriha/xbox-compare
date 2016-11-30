import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

import { Game }                from '../game/game';
import { GameService }         from '../game/game.service';

@Component({
    selector: 'my-games',
    templateUrl: 'games.component.html',
    styleUrls: [ 'games.component.css' ]
})
export class GamesComponent implements OnInit {
    games: Game[];

    constructor(
        private gameService: GameService,
        private router: Router) { }

    getGames(): void {
        this.gameService
            .getGames()
            .subscribe((games: Game[]) => {
                this.games = games;
                console.log(games);
            }, (err: any) => {
                console.log(err);
                this.games = null;
            }, () => console.log('Done')); // voila je sais aps j'ai apris comme ça regarde

    }
    ngOnInit(): void {
        this.getGames();
    }
}


/*
 Copyright 2016 Google Inc. All Rights Reserved.
 Use of this source code is governed by an MIT-style license that
 can be found in the LICENSE file at http://angular.io/license
 */