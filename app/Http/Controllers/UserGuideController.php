<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class UserGuideController extends Controller
{
    public function download()
    {
        return Pdf::loadView('user-guide')->download('leave-management-user-guide.pdf');
    }
}
