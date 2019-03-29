<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function test1()
    {
        return response()->json(['a' => 1, 'b' => 2]);
    }
}
