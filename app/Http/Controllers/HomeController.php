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
		$posts = Post::with([
				'user:id,name',
				'user.profile:id,user_id,slug',
				'tags:id,title,slug',
			])
			->orderByDesc('id')
			->paginate(10);

		return view('home', compact('posts'));
	}
}
