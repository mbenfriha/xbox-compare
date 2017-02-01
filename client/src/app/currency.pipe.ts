import { Pipe, PipeTransform, Injectable } from '@angular/core';
import { Headers, Http, Response } from '@angular/http';
import myGlobals = require('./globals');

@Pipe({name: 'convertCurrency'})
export class Currency implements PipeTransform {

    data: {rates: {USD: number, CHF: number}};

    constructor(private http: Http) {
        http.get('http://api.fixer.io/latest?base=EUR')
            .map((res: Response) => res.json())
            .subscribe(res => {
                this.data = res;
            })
    }

    transform(value: number, args: any): any {

        if(args === '$') {
            return (value * this.data.rates.USD).toFixed(2) + ' $';
        }
        if(args === 'CHF') {
            return (value * this.data.rates.CHF).toFixed(2) + ' CHF';
        }
        return value + ' €'
    }
}