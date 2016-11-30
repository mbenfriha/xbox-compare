import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { GamesComponent }  from './games/games.component';



const routes: Routes = [
    { path: '', redirectTo: '/', pathMatch: 'full' },
    { path: 'games',     component: GamesComponent }
];
@NgModule({
    imports: [ RouterModule.forRoot(routes) ],
    exports: [ RouterModule ]
})
export class AppRoutingModule {}