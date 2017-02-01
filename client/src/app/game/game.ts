export class Game {
    id:string;
    name:string;
    prices: Array<Prices>;
    created_at:string;
    updated_at:string;
    addon_id: boolean;
    description: string;
    discount: boolean;
    gamescore: number;
    slug: string;
    studio: string;
    type: string;
    video: string;

}

class Prices {
    country: string;
    countr_id: number;
    created_at: string;
    discount: number;
    euro_value: number;
    euro_value_discount: number;
    game_id: number;
    id: number;
    updated_at: string;
    value: number;
    value_discount: number;
}