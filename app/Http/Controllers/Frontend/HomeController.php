<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Helpers\Collection;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use App\Models\Paket;
use App\Models\Landing\Video;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $banner = [
            '/images/banner/Agafia-Adda-Mandiri.jpg',
            '/images/banner/Agafia-Adda-Mandiri-PT-1.jpg'
        ];

        return Inertia::render('Home',[
            'banner' => $banner
        ]);
    }
}
