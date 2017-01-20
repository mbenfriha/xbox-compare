import 'rxjs/add/operator/switchMap';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Params }   from '@angular/router';
import { Location }                 from '@angular/common';


import { Game }                from './game';
import { GameService }         from './game.service';

import myGlobals = require('../globals');

@Component({
    templateUrl: 'game.component.html',
    styleUrls: [ 'game.component.css' ]
})
export class GameComponent implements OnInit {
    game: Game;
    id: any;
    private sub: any;
    link = "http://api.xbox-store-compare.com/img/";
    background = "/background.jpeg";
    cover = "/cover.jpeg";
    minValue: number;
    maxValue: number;
    trophee = false;
    error = false;

    get myGlobals() { return myGlobals; }


    constructor(
        private gameService: GameService,
        private route: ActivatedRoute) { }

    getGame(id: any): void {
        this.gameService
            .getGame(id)
            .subscribe((game: Game) => {
                this.game = game;

                function finder(cmp: any, arr: any, attr: any) {
                    var val = arr[0][attr];
                    for(var i=1;i<arr.length;i++) {
                        if(cmp(val, arr[i][attr]) > 0)
                            val = cmp(val, arr[i][attr])
                    }
                        return val;
                }

              /*  var lowest = Number.POSITIVE_INFINITY;
                var highest = Number.NEGATIVE_INFINITY;
                var tmp;
                console.log(highest);
                for (var i=this.game.prices.length-1; i>=0; i--) {
                    tmp = this.game.prices[i].euro_value;
                    if (tmp < lowest && tmp > 0) lowest = tmp;
                    if (tmp > highest) highest = tmp;
                }*/
                this.minValue = finder(Math.min, this.game.prices, "euro_value");
                this.maxValue = finder(Math.max, this.game.prices, "euro_value");
            }, (err: any) => {
                console.log(err);
                this.game = null;
            });

    }

    toggleTrophee() {
            this.gameService
            .refreshGame(this.game.id)
            .subscribe((response: any) => {
                   if(response.message == "time")
                       this.error = true;
                    else
                        this.trophee = true;
            }, (err: any) => {
                    console.log('erreur');
            });

    }
    ngOnInit(): void {
        this.sub = this.route.params.subscribe(params => {
            this.id = params['id'];
            this.getGame(this.id);
        });
    }

    ngAfterViewInit() {
    }

}