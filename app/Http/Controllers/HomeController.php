<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View as ViewContract;

class HomeController extends Controller {
	/**
	 * Create a new controller instance.
	 *
	 */
	public function __construct() {
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return ViewContract
	 */
	public function index(): ViewContract {
		$posts = Post::orderByDesc( 'id' )->paginate( 10 )->onEachSide( 0 );

		return view( 'home', compact( 'posts' ) );
	}
}
