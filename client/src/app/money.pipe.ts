import { Pipe, PipeTransform, Injectable } from '@angular/core';
import { Headers, Http, Response } from '@angular/http';

@Pipe({name: 'money'})
export class Money implements PipeTransform {


    constructor() {}

    transform(value: number, args: any): any {

        switch(args) {
            case 'ZAR':
                return 'R ' + value;
            case 'ARS':
                return '$ ' + value;
            case 'BRL':
                return 'R$ ' + value;
            case 'CAD':
                return value + ' $';
            case 'COP':
                return '$' + value;
            case 'EUR':
                return  value + ' €';
            case 'HKD':
                return '$' + value;
            case 'INR':
                return '₹ ' + value;
            case 'JPY':
                return '¥' + value;
            case 'HUF':
                return value + ' HUF';
            case 'MXN':
                return '$' + value;
            case 'RUB':
                return value + ' ₽';
            case 'SGD':
                return 'SG$'+ value +' incl. GST';
            case 'TWD':
                return 'NT$'+ value ;
            case 'USD':
                return '$'+value ;
        }
    }
}