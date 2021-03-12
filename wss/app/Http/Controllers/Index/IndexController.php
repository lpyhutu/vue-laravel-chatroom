<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;


class IndexController extends Controller
{

    protected function index()
    {

//        return  hash('sha256', 111);
        return view("index");
    }
}
