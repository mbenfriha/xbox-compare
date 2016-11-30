import { NgModule }       from '@angular/core';
import { BrowserModule }  from '@angular/platform-browser';
import { FormsModule }    from '@angular/forms';
import { HttpModule }    from '@angular/http';


import { AppComponent }         from './app.component';
import { GamesComponent }      from './games/games.component';
import { GameService }          from './game/game.service';

import { AppRoutingModule }     from './app-routing.module';

@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        HttpModule,
        AppRoutingModule,
    ],
    declarations: [
        AppComponent,
        GamesComponent,

    ],
    bootstrap: [ AppComponent ],
    providers: [ GameService ],
})
export class AppModule { }