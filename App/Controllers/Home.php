<?php

namespace App\Controllers;

use App\Models\Posts;
use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
		$posts = Posts::getAll();

        View::renderTemplate('Home/index.html', ['posts' => $posts]);
    }

	public function showAction ()
	{
		try {
			$id   = $this->route_params['id'];
			$post = Posts::getOne( $id );
		} catch ( \Exception $e ) {
			echo $e->getMessage();
		}
		View::renderTemplate('Home/show.html', ['post' => $post]);
    }
}
