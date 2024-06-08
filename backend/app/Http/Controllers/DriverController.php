<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
 
    public function show(Request $request)
    {
        //reteurn back the user and associated driver model

        $user = $request->user();
        $user->load('driver');

        //driver
        return $user;
    }

    public function update(Request $request)
    {
        $request->validate([
            'year' => 'required|numeric|',
            'make' => 'required',
            'model' => 'required',
            'color' =>'required|alpha',
            'license_plate' => 'required',
            'name' => 'required'
        ]);

        $user = $request->user();
        $user->update($request->only('name'));

        //create or update a driver associated with this user
        $user->driver()->updateOrCreate($request->only([
            'year',
            'model',
            'color',
            'make',
            'license_plate'
        ]));

        $user->load('driver');

        return $user;

    }

}
