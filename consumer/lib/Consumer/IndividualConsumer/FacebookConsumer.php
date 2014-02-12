<?php
namespace Consumer\IndividualConsumer;

use Consumer\Log;

class FacebookConsumer extends IndividualConsumer
{
	public function consume($filter) {
		parent::consume($filter);

		$parameters = array(
			'q' => str_replace(PHP_EOL, ' ', $filter->query_simple),
			'type' => 'post'
		);

		$url = 'https://graph.facebook.com/search?' . http_build_query($parameters);
		$content = file_get_contents($url);
		$response = json_decode($content);

		foreach ($response->data as $status)
		{
	        $interaction = array(
	            'interaction' => array(
	                'schema' => array('version' => 3),
	                'author' => array(
	                    'username' => $status->from->id,
	                    'name' => $status->from->name,
	                    'id' => $status->from->id,
	                    'avatar' => 'https://graph.facebook.com/picture?id=' . $status->from->id,
	                    'link' => 'http://facebook.com/profile.php?id=' . $status->from->id
	                ),
	                'type' => 'facebook',
	                'created_at' => new \MongoDate(time() + rand(0, 60*4)), // Necessary when data is not live
	                'content' =>  isset($status->message) ? $status->message : NULL,
	                'id' => $status->id,
	                'link' => isset($status->link) ? $status->link : NULL,
	            ),
	            'facebook' => $status,
	            'internal' => array(
	                'filter_id' => (int)$filter->id
	            )
	        );

	        \Consumer\Interaction::insert($interaction);
		}
	}	
}