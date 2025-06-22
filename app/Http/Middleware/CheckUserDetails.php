<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserDetails
{
    protected $except = [
        'user.anak.store',
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        foreach ($this->except as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        $user = Auth::guard('web')->user();

        // Check if the user has filled the 'detail' table
        $hasDetails = $user->detail()->exists();

        if (!$hasDetails || !$hasChildren) {
            return redirect()->route('user.biodata.form')->with('error', 'Please complete your profile details.');
        }

        return $next($request);
    }
}
