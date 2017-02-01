import { Component } from '@angular/core';
import { TranslateService } from 'ng2-translate';
import myGlobals = require('./globals');
import { CompleterService, CompleterData, CompleterItem } from 'ng2-completer';
import { GameService } from './game/game.service';
import { Router } from '@angular/router';
declare var jQuery:any;

@Component({
    selector: 'my-app',
    templateUrl: './app.component.html',
    styleUrls: ['app.component.css'],
})


export class AppComponent {
    toggle = true;
    currencyCurrent = '$';
    private searchStr: string;
    //private dataService: CompleterData;
    private searchData: any[] = [];


    constructor(private translate: TranslateService,
                //private completerService: CompleterService,
                private gameService: GameService,
                private router: Router) {
        translate.addLangs(["en", "fr"]);
        translate.setDefaultLang('fr');

        let browserLang = translate.getBrowserLang();
        translate.use(browserLang.match(/en|fr/) ? browserLang : 'en');
    }

    findGame(name: string) {
        if(name.length > 1) {
            this.gameService
                .findGame(name)
                .subscribe((response:any) => {
                    this.searchData = response;
                    //this.dataService = this.completerService.local(this.searchData, 'name', 'name');
                    console.log(response);

                }, (err:any) => {
                });
        }
        else
            this.searchData = [];
    }

    onChange(name: any){
        console.log(name);
    }

    onGameSelected(id: string) {
        if (id) {
            this.router.navigate(['/game', id]);
            this.searchData = []
            this.searchStr = "";
        }
    }



    toggleCurrency(sym: string) {

        myGlobals.currency = sym;
        this.currencyCurrent = sym;
    }

    ngAfterViewInit() {
    }
}