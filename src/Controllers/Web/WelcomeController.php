<?php
namespace Philip0514\Ark\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        return view('welcome');
    }
}