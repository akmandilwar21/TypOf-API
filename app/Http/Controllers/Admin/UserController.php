<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        try {
            return 'Yes!! Admin namespace is working successfully';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
