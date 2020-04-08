<?php
namespace Philip0514\Ark\Controllers\API\V1_0;

use Illuminate\Support\Facades\Mail;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Philip0514\Ark\Mail\User;

class MailController extends Controller
{
    public function test(Request $request)
    {
        $data = [];
        /*
        Mail::send('ark::mail.test', $data, function($message)
        {
            $message->to('philip0514@gmail.com', 'John Smith')->subject('Welcome!');
        });
        */

        dd($request->user());
        $data = [
            'a' =>  1,
        ];
        Mail::to($request->user())->send(new User($data));
    }
}