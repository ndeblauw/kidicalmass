<?php

namespace App\Http\Controllers;

use App\Support\Build\BuildStatus;

class BuildDashboardController extends Controller
{
    public function __invoke(BuildStatus $status)
    {
        return view('build.dashboard', $status->report());
    }
}
