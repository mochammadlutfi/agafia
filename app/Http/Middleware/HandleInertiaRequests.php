<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;
use Request as Req;
use Illuminate\Support\Facades\Auth;
use App\Helpers\GeneralHelp;
use App\Helpers\MenuHelper;
use App\Models\Dapil;
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // return array_merge(parent::share($request), [
        //     'auth' => [
        //         'user' => $request->user(),
        //     ],
        // ]);
        $data = [
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
            // 'ziggy' => function () use ($request) {
            //     return array_merge((new Ziggy)->toArray(), [
            //         'location' => $request->url(),
            //     ]);
            // },
        ];
        
        if(Req::segment(1) == 'admin'){
            
            if(Auth::guard('admin')->check()){
                $data['menu'] = MenuHelper::get();
                
                $data['user'] = $request->user('admin')->only('nama', 'email', 'username','level', 'level_label');
            }
        }
        
        if(Req::segment(1) != 'admin' && Auth::guard('web')->check()){
            if(Req::segment(1) == 'user'){
                $data['menu'] = MenuHelper::user();
            }
            
            $data['user'] = $request->user('web')->only('nama', 'email', 'initial_avatar');
            // $data['user']['initial'] => 
        }
        return array_merge(parent::share($request), $data);
    }

    
    protected function sharedValidationErrors(){
        if($errors = session('errors')){
            return $errors->getBag('default');
        }
        return (object)[];
    }
}
