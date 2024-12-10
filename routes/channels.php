<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});

//Broadcast::channel('private:HoC.Stock', function (User $user){
//    return true;
//});

// not working
Broadcast::channel('HoC.Stock2', function (User $user){
    return true;
});
