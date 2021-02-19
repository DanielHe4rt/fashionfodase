<?php

namespace App\Http\Controllers;

class ExampleController extends Controller
{
    public function getWelcome() {
        return app()->version();
    }

}
