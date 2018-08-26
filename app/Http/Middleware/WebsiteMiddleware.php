<?php namespace App\Http\Middleware;

use Closure;

class WebsiteMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(!\Sentry::check())
		{	
			echo $_SERVER['request_uri'];
			// $last_url = trim($_SERVER['HTTP_REFERER']);
			return \Redirect::to('user/load');
		}
		return $next($request);
	}

}
