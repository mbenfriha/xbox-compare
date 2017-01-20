import { Component } from '@angular/core';
import { TranslateService } from 'ng2-translate';
import myGlobals = require('./globals');


@Component({
    selector: 'my-app',
    templateUrl: './app.component.html',
    styleUrls: ['app.component.css'],
})


export class AppComponent {
    toggle = true;
    currencyCurrent = '$';

    constructor(private translate: TranslateService) {
        translate.addLangs(["en", "fr"]);
        translate.setDefaultLang('fr');

        let browserLang = translate.getBrowserLang();
        translate.use(browserLang.match(/en|fr/) ? browserLang : 'en');
    }

    get currency() { return this.toggle ? '€' : '$'; }
    get currencyCo() { return this.toggle ? '$' : '€'; }

    toggleCurrency() {
        this.toggle = !this.toggle;
        myGlobals.currency = this.currency;
        this.currencyCurrent = this.currencyCo;
    }
}