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

		for($i = 0; $i < count($posts); $i++){
			$posts[$i]['post_title'] = $this->cuttingStr($posts[$i]['post_title'], 50);
		}

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

	public function cuttingStr( $string, $length ) {
		$string = strip_tags($string);
		$string = mb_substr($string, 0, $length);
		$string = rtrim($string, "!,.-");
		$string = mb_substr($string, 0, strrpos($string, ' '));
		return $string . '...';
	}
}
