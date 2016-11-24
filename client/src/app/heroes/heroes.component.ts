
import { Component, OnInit } from '@angular/core';
import '../../public/css/styles.css';

import { Hero } from './hero/hero';
import { HeroService } from './hero/hero.service';



@Component({
    selector: 'my-heroes',
    providers: [HeroService],
    templateUrl: './heroes.component.html',
    styleUrls: ['./heroes.component.css'],
})

export class HeroesComponent implements OnInit {

    title = 'Tour of Heroes';
    heroes: Hero[];
    selectedHero: Hero;

    constructor(private heroService: HeroService) { }

    getHeroes(): void {
        this.heroService.getHeroes().then(heroes => this.heroes = heroes);
    }

    ngOnInit(): void {
        this.getHeroes();
    }


    onSelect(hero: Hero): void {
        this.selectedHero = hero;
    }

}