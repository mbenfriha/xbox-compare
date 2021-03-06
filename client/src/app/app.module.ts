import { NgModule }       from '@angular/core';
import { BrowserModule }  from '@angular/platform-browser';
import { FormsModule }    from '@angular/forms';
import { HttpModule }    from '@angular/http';
import { TranslateModule } from 'ng2-translate';
//import { Ng2CompleterModule } from "ng2-completer";


import { AppComponent }         from './app.component';
import { GamesComponent }      from './games/games.component';
import { GameComponent }      from './game/game.component';
import { HomeComponent }      from './home/home.component';
import { SliderComponent }      from './slider/slider.component';
import { AddComponent }      from './add/add.component';
import { BuyComponent }      from './buy/buy.component';

import { GameService }          from './game/game.service';

import { AppRoutingModule }     from './app-routing.module';


import { Currency }     from './currency.pipe';
import { Money }     from './money.pipe';

@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        HttpModule,
        AppRoutingModule,
        TranslateModule.forRoot(),
        //Ng2CompleterModule,
    ],
    declarations: [
        AppComponent,
        GamesComponent,
        GameComponent,
        HomeComponent,
        SliderComponent,
        AddComponent,
        BuyComponent,
        Currency,
        Money

    ],
    bootstrap: [ AppComponent ],
    providers: [ GameService ],
})
export class AppModule { }