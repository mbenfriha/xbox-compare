import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { GamesComponent }  from './games/games.component';
import { GameComponent }  from './game/game.component';
import { HomeComponent }  from './home/home.component';
import { AddComponent }  from './add/add.component';
import { BuyComponent }  from './buy/buy.component';



const routes: Routes = [
    {   path: '',
        component: HomeComponent
    },
    {   path: 'games/:nbr',
        component: GamesComponent
    },
    {   path: 'game/:id',
        component: GameComponent
    },
    {   path: 'add',
        component: AddComponent
    },
    {   path: 'buy',
        component: BuyComponent
    },
];
@NgModule({
    imports: [ RouterModule.forRoot(routes) ],
    exports: [ RouterModule ]
})
export class AppRoutingModule {}