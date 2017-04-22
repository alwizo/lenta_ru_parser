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

		$articles = [];

		for ( $i = 0; $i < 5; $i ++ ) {

			$title = $xml->channel->item[ $i ]->title;
			$articles[$i]['title'] = $title;
			$link =  $xml->channel->item[ $i ]->link;
			$articles[$i]['link'] = $link;

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
					$articles[$i]['img'] = 'Нет изображения';
				}

				$text = str_get_html(strip_tags($element, '<p></p><a></a><img><script></script>'));
				$articles[$i]['text'] = $text;
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


		Posts::getAllToCsv($output);

	}
}