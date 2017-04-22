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

    public static function insertAll ($articles)
    {
    	$db = static::getDB();

    	foreach ($articles as $article){
		    $title = $article['title'];
		    $text = $article['text'];
		    $img = $article['img'];
		    $link = $article['link'];
		    $stmt = $db->prepare("INSERT INTO posts (post_title, post_text, post_img, post_link) 
								VALUES (:title, :text, :img, :link) 
								ON DUPLICATE KEY UPDATE post_id=post_id");
		    $stmt->bindParam(':title', $title);
		    $stmt->bindParam(':text', $text);
		    $stmt->bindParam(':img', $img);
		    $stmt->bindParam(':link', $link);
		    $stmt->execute();
	    }
    }

	public static function getAllToCsv($output)
	{
		$db = static::getDB();
		$stmt = $db->prepare('SELECT post_title, post_pubdate, post_link FROM posts
								WHERE post_pubdate = 2
 								ORDER BY post_pubdate DESC ');
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

			echo '<pre>';
			var_dump($row);
			echo '</pre>';
			fputcsv($output, $row);
		}

    }
}
