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
    error = false;
    wait = false;

    constructor(
        private gameService: GameService,
        private router: Router) { }


    addGame(link: string) {
        this.wait = true;
        this.gameService
            .addGame(link)
            .subscribe((response: any) => {
                if(response.message == false){
                    this.wait = false;
                    this.error = true;
                }
                else if(response.message == "exist"){
                    this.router.navigate(['/game', response.game.id]);
                }
                else
                this.router.navigate(['/game', response.message.id]);
            }, (err: any) => {
                console.log('erreur');
                this.wait = false;
                this.error = true;
            });

    }

    ngOnInit(): void {}
}