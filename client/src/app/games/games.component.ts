import 'rxjs/add/operator/switchMap';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Params }   from '@angular/router';
import { Location }                 from '@angular/common';

import { Game }                from '../game/game';
import { GameService }         from '../game/game.service';

import myGlobals = require('../globals');

@Component({
    templateUrl: 'games.component.html',
    styleUrls: [ 'games.component.css' ]
})
export class GamesComponent implements OnInit {
    games: Game[];

    private sub: any;
    link = "http://xbox:8889/img/";
    background = "/background.jpeg";
    cover = "/cover.jpeg";
    page = 1;
    order = "name";
    asc = "asc";
    price = 1000;
    nbr: any[] = [];
    tAsc = true;
    empty= true;


    get myGlobals() { return myGlobals; }


    constructor(
        private gameService: GameService,
        private route: ActivatedRoute) { }


    getGamesFilter(page: number, order: string, asc: string, price: number): void {
        this.gameService
            .getGamesFilter(page, order, asc, price)
            .subscribe((games: any) => {
                if(games.game == 'empty')
                    this.empty = true;
                else {
                    this.games = games;
                    this.empty = false;
                    this.nbr = [];
                    for(let i = 0; i < games.nbr_page; i++)  {
                        this.nbr.push(i);
                    }

                    console.log(this.nbr);
                    console.log(games);
                }
            }, (err: any) => {
                console.log(err);
                this.games = null;
            }, () => console.log('Done'));
    }

    setAsc(asc: string) {
        this.asc = asc;
        this.sub = this.route.params.subscribe(params => {
            this.page = params['nbr'];
            this.getGamesFilter(this.page, this.order, this.asc, this.price);
        });
    }
    setOrder(order: string) {
        this.order = order;
        this.sub = this.route.params.subscribe(params => {
            this.page = params['nbr'];
            this.getGamesFilter(this.page, this.order, this.asc, this.price);
        });
    }

    setPrice(price: number) {
        this.price = price;
        this.sub = this.route.params.subscribe(params => {
            this.page = params['nbr'];
            this.getGamesFilter(this.page, this.order, this.asc, this.price);
        });
    }

    getPrice(prices: any, amount: string) {
        function finder(cmp: any, arr: any, attr: any) {
            var val = arr[0][attr];
            for(var i=1;i<arr.length;i++) {
                if(cmp(val, arr[i][attr]) > 0)
                    val = cmp(val, arr[i][attr])
            }
            return val;
        }


        if(amount == '-')
            return finder(Math.min, prices, "euro_value");
        else
            return finder(Math.max, prices, "euro_value");

    }
    ngOnInit(): void {
        this.sub = this.route.params.subscribe(params => {
            this.page = params['nbr'];
            this.getGamesFilter(this.page, this.order, this.asc, this.price);
        });
    }

    ngAfterViewInit() {
    }
}