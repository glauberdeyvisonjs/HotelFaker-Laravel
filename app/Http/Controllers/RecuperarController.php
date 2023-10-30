<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class RecuperarController extends Controller
{
    /**
     * @return Factory|Application|View
     */
    public function view(): Factory|Application|View
    {
        return view('site.recuperar');
    }
}
