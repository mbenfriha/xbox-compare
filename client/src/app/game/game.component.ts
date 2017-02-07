import 'rxjs/add/operator/switchMap';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Params }   from '@angular/router';
import { Location }                 from '@angular/common';
import {DomSanitizer} from '@angular/platform-browser';



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
    link = myGlobals.api_url + "img/";
    background = "/background.jpeg";
    cover = "/cover.jpeg";
    minValue: number;
    maxValue: number;
    trophee = false;
    error = false;
    wait = false;
    mailRegister = false;
    addons: any[] = [];
    showAddons = false;
    video = '';
    baseUrl: string = 'https://www.youtube.com/embed/';

    get myGlobals() { return myGlobals; }


    constructor(
        private gameService: GameService,
        private route: ActivatedRoute,
        private sanitizer: DomSanitizer) { }

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
                this.minValue = finder(Math.min, this.game.prices, "euro_value");
                this.maxValue = finder(Math.max, this.game.prices, "euro_value");

                this.video = <string>this.sanitizer.bypassSecurityTrustResourceUrl(this.baseUrl + this.game.video);
            }, (err: any) => {
                console.log(err);
                this.game = null;
            });

    }

    getAddons(id: string): void {
        this.gameService
            .getAddons(id)
            .subscribe((addons: any) => {
                this.addons = addons;
                console.log(addons)
            }, (err: any) => {
                console.log(err);
                this.addons = null;
            });
    }
     validateEmail(email: string) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

    send(price: number, email: string) {
        if(price && this.validateEmail(email)) {
            this.gameService
                .sendMail(this.id, email, price)
                .subscribe((response: any) => {
                    console.log(response);
                    this.mailRegister = true;
                }, (err: any) => {
                    this.mailRegister = false;
                    console.log('erreur');
                });
        }
    }

    toggleTrophee() {
        this.wait = true;
            this.gameService
            .refreshGame(this.game.id)
            .subscribe((response: any) => {
                   if(response.message == "time") {
                       this.error = true;
                       this.wait = false;
                   }
                    else {
                       this.trophee = true;
                       this.wait = false;
                   }
                    console.log(response);
            }, (err: any) => {
                    console.log('erreur');
                    this.wait = false;
            });
    }
    toggleAddons() {
        this.showAddons = !this.showAddons
    }
    ngOnInit(): void {
        this.sub = this.route.params.subscribe(params => {
            this.id = params['id'];
            this.getGame(this.id);
            this.getAddons(this.id);
        });
    }

    ngAfterViewInit() {
    }

}