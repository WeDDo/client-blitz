<?php

use Illuminate\Support\Facades\Broadcast;

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

Broadcast::routes();

Broadcast::channel('files', function ($user) {
    return true;
});

Broadcast::channel('ripper', function ($user) {
    return true;
});
