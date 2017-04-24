<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Posts extends \Core\Model
{

    /**
     * Get all the users as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM posts ORDER BY post_id DESC ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getOne($id)
	{
		$db = static::getDB();

		$stmt = $db->prepare('SELECT post_id, post_title, post_text, post_link, post_img 
								FROM posts WHERE post_id = :id');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$post = $stmt->fetch(PDO::FETCH_ASSOC);
		return $post;
    }

	/**
	 * @param $articles
	 */
	public static function insertAll ($articles)
    {
    	$db = static::getDB();

    	foreach ($articles as $article){
		    $title = $article['title'];
		    $pubdate = $article['pubdate'];
		    $text = $article['text'];
		    $img = $article['img'];
		    $link = $article['link'];
		    $stmt = $db->prepare("INSERT INTO posts (post_pubdate, post_title, post_text, post_img, post_link) 
								VALUES (:pubdate, :title, :text, :img, :link) 
								ON DUPLICATE KEY UPDATE post_id=post_id");
		    $stmt->bindParam(':title', $title);
		    $stmt->bindParam(':pubdate', $pubdate);
		    $stmt->bindParam(':text', $text);
		    $stmt->bindParam(':img', $img);
		    $stmt->bindParam(':link', $link);
		    $stmt->execute();
	    }
    }

	/**
	 * @param $last_day
	 *
	 * @return mixed
	 */
	public static function getDaily($last_day)
	{
		$db = static::getDB();
		$stmt = $db->prepare('SELECT post_title, post_pubdate, post_link 
								FROM posts
								WHERE post_pubdate > :last_day
 								ORDER BY post_pubdate DESC ');
		$stmt->bindParam(':last_day', $last_day);
		$stmt->execute();
		$daily_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $daily_posts;
    }

	public static function dateOfLastNews () {
		$db = static::getDB();
		$stmt = $db->query('SELECT MAX(post_pubdate)
								FROM posts
								 ');
		$date = $stmt->fetch(PDO::FETCH_ASSOC);

		return $date['MAX(post_pubdate)'];
    }
}
