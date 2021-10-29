<?php

namespace App\Repositories\Pos;

//Interface
use App\Contracts\Pos\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface{

    public function createCart($model, $id){
        $cart = new $model();
        $cart->user_id = $id;
        $cart->save();
        return $cart;
    }
}