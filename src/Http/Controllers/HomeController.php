<?php

namespace Davidhsianturi\Compass\Http\Controllers;

use Davidhsianturi\Compass\Compass;
use Illuminate\Support\Facades\Auth;

class HomeController
{
    /**
     * Display the Compass view.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        if (! \Auth::check()) {
            return redirect('login');
        }
        return view('compass::layout', [
            'compassScriptVariables' => Compass::scriptVariables(),
            'assetsAreCurrent' => Compass::assetsAreCurrent(),
        ]);
    }
}
