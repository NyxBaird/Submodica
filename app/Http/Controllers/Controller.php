<?php

namespace App\Http\Controllers;

use App\Http\Services\Respond;
use App\Mail\Contact;
use App\Mail\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $user = false;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            //Do middleware tasks?

            return $next($request);
        });
    }

    public function contact(Request $request)
    {
        if (!Auth::user())
            Respond::error("You must be logged contact the admin!");

        if (!$request->post('subject', false) || !$request->post('body', false))
            Respond::error("Please provide a subject and body for this message!");

        $contact = $request->post();
        $contact['user'] = Auth::user();

        Mail::to('admin@submodica.xyz')->send(new Contact($contact));

        Respond::success("Your message has been sent!");
    }
}
