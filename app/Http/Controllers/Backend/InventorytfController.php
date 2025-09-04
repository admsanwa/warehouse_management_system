<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Http;
use Illuminate\Http\Request;

class InventorytfController extends Controller
{
    public function create() {}

    public function list()
    {
        return view('backend.inventorytf.list');
    }

    public function view()
    {
        return view('backend.inventorytf.view');
    }
}
