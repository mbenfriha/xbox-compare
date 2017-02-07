import { Component, OnInit, AfterViewInit } from '@angular/core';
import { Router }            from '@angular/router';

import { Game }                from '../game/game';
import { GameService }         from '../game/game.service';

declare var jQuery:any;

import myGlobals = require('../globals');

@Component({
    selector: 'my-games',
    templateUrl: 'home.component.html',
    styleUrls: [ 'home.component.css' ]
})
export class HomeComponent implements OnInit {
    golds: Game[];
    link = myGlobals.api_url+"img/";
    img = "/cover.jpeg";

    constructor(
        private gameService: GameService,
        private router: Router) {}

    getGolds(): void {
        this.gameService
            .getGold()
            .subscribe((games: Game[]) => {
                this.golds = games;
            }, (err: any) => {
                console.log(err);
                this.golds = null;
            }, () => console.log(''));
    }

    ngAfterViewInit() {

    }

    ngOnInit(): void {
        this.getGolds();
    }
}