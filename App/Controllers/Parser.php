<?php


namespace App\Controllers;


use App\Models\Posts;

class Parser extends \Core\Controller
{

	public $file_name = "news.xml";
	public $rss_url = "https://lenta.ru/rss";

	public function parseNewsAction()
	{
		set_time_limit(0);

		//$limit = $this->route_params['limit'];

		$xml = simplexml_load_file( $this->rss_url );

		$items = [];
		foreach ( $xml->channel->item as $item ) {
			$items[] = $item;
		}



		//узнать дату последней новости
		 $last_news = Posts::dateOfLastNews();

		//если в массиве $items есть новости позже этой даты - спарсить их

		$articles = [];

		for ( $i = 0; $i < count($items); $i ++ ) {

			if(strtotime($xml->channel->item[ $i ]->pubDate) > strtotime($last_news)){
				$title = $xml->channel->item[ $i ]->title;
				$articles[$i]['title'] = $title;
				$link =  $xml->channel->item[ $i ]->link;
				$articles[$i]['link'] = $link;
				$pubdate = $xml->channel->item[ $i ]->pubDate;
				$pubdate = date('Y-m-d H:i:s', strtotime($pubdate));
				$articles[$i]['pubdate'] = $pubdate;

				$html = file_get_html("$link");
				$e = $html->find('div[itemprop=articleBody]');

				foreach($e as $element){

					if($asides = $element->find('aside')){
						foreach ($asides as $aside){
							$aside->outertext = '';
						}

					}

					if($img = $html->find('img[itemprop=image]', 0)){
						$articles[$i]['img'] = $img->getAttribute('src');
					}else{
						$articles[$i]['img'] = '';
					}

					$text = str_get_html(strip_tags($element, '<p></p><a></a><img><script></script>'));
					$articles[$i]['text'] = $text;
				}

			}

		}

		Posts::insertAll($articles);
		header('Location: /');
		exit;
	}

	public function dailyNewstoCsvAction()
	{

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; daily_news.csv');


		$output = fopen('php://output', 'w');


		fputcsv($output, array('Название новости', 'Дата', 'Оригинальная ссылка на новость'));
		$today = date('Y-m-d H:i:s', strtotime('last day'));

		$daily_posts = Posts::getDaily($today);

		foreach ( $daily_posts as $daily_post ) {
			fputcsv($output, $daily_post);
		}
	}
}