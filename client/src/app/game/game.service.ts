import { Injectable }    from '@angular/core';
import { Headers, Http } from '@angular/http';

import 'rxjs/add/operator/toPromise';
import {Observable} from 'rxjs/Observable';

import { Game } from './game';

@Injectable()
export class GameService {

    private headers = new Headers({'Content-Type': 'application/json'});
    private apiUrl = 'http://api.xbox-store-compare.com/';  // URL to web api

    constructor(private http: Http) { }

    getGames(): Observable<Array<Game>> {
        return this.http.get(this.apiUrl + 'games')
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });

       /* return this.http.get(this.apiUrl + 'games')
            .toPromise()
            .then(response => response.json().data as Game[]) // bizare
            .catch(this.handleError); */
    }
    getGamesFilter(page: number, order: string, asc: string, price: number, type: string): Observable<Array<Game>> {

        return this.http.get(this.apiUrl + 'game/filter?page='+ page +'&asc='+ asc +'&order='+ order +'&price='+ price +'&type='+ type)
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }

    getLastGames(): Observable<Array<Game>> {
        return this.http.get(this.apiUrl + 'games/last')
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }

    getGame(id: any): Observable<Game> {
        return this.http.get(this.apiUrl + 'game/' + id)
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }

    addGame(link: string): Observable<Game>{
        return this.http.post(this.apiUrl + 'game', {link})
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }
    findGame(name: string): Observable<Game>{
        return this.http.post(this.apiUrl + 'game/find', {name})
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }

    refreshGame(id: any): Observable<Game> {
        return this.http.get(this.apiUrl + 'game/refresh/' + id)
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }

    getGold(): Observable<Array<Game>> {
        return this.http.get(this.apiUrl + 'gold')
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }
    getAddons(id: string): Observable<Game> {
        return this.http.get(this.apiUrl + 'game/addons/' + id)
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }

    sendMail(game_id: string, email: string, price: number): Observable<Game>{
        return this.http.post(this.apiUrl + 'alert', {game_id, price, email})
            .map(res => res.json())
            .catch((e) => {
                return Observable.throw(
                    new Error(`${ e.status } ${ e.statusText }`)
                );
            });
    }

    private handleError(error: any): Promise<any> {
        console.error('An error occurred', error); // for demo purposes only ici peu ere
        return Promise.reject(error.message || error);
    }
}



/*
 Copyright 2016 Google Inc. All Rights Reserved.
 Use of this source code is governed by an MIT-style license that
 can be found in the LICENSE file at http://angular.io/license
 */