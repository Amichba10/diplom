<?php

namespace App\Http\Controllers;

use App\Models\Exceptional;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Exceptional::paginate(5);
        return view('users',compact('data'));
    }
}
