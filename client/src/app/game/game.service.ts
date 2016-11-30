import { Injectable }    from '@angular/core';
import { Headers, Http } from '@angular/http';

import 'rxjs/add/operator/toPromise';
import {Observable} from 'rxjs/Observable';

import { Game } from './game';

@Injectable()
export class GameService {

    private headers = new Headers({'Content-Type': 'application/json'});
    private apiUrl = 'http://xbox:8889/';  // URL to web api

    constructor(private http: Http) { }

    getGames(): Observable<Game[]> {
        return this.http.get(this.apiUrl + 'games')
            .map(res => res.json())
            .catch(this.handleError);

       /* return this.http.get(this.apiUrl + 'games')
            .toPromise()
            .then(response => response.json().data as Game[]) // bizare
            .catch(this.handleError); */
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