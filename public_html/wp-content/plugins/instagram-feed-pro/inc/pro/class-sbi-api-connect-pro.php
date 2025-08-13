<?php

if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Class SB_Instagram_API_Connect_Pro
 *
 * Adds support for additional endpoints:
 *
 * - Personal account comments
 * - Business account top and recent hashtags
 * - Business account stories
 * - Business account comments
 * - Business account hashtag IDs
 *
 * @since 5.0
 */
class SB_Instagram_API_Connect_Pro extends SB_Instagram_API_Connect
{
	/**
	 * The response from the API request
	 *
	 * @var array
	 */
	protected $response;

	/**
	 * Determines if the given type allows actions after paging.
	 *
	 * @param string $type The type to check.
	 * @return bool True if the type allows actions after paging, false otherwise.
	 */
	public function type_allows_after_paging($type)
	{
		return $type === 'tagged';
	}

	/**
	 * Builds the Facebook API URL for the given connected account.
	 *
	 * @param array  $connected_account The connected account identifier.
	 * @param string $endpoint The API endpoint to connect to.
	 * @param array  $params Additional params related to the request.
	 * @param string $access_token The access token for authentication.
	 *
	 * @return string The constructed Facebook API URL.
	 */
	protected function buildFacebookUrl($connected_account, $endpoint, $params, $access_token)
	{
		$num = !empty($params['num']) ? (int)$params['num'] : 33;
		$num = min($num, 200);
		$paging = isset($params['cursor']) ? '&after=' . $params['cursor'] : '';

		$header_fields = 'biography,id,username,website,followers_count,media_count,profile_picture_url,name';
		$media_fields = 'media_url,media_product_type,caption,id,media_type,timestamp,comments_count,like_count,permalink,children%7Bmedia_url,id,media_type,permalink%7D';
		$media_fields_default = 'media_url,media_product_type,thumbnail_url,caption,id,media_type,timestamp,username,comments_count,like_count,permalink,children%7Bmedia_url,id,media_type,timestamp,permalink,thumbnail_url%7D';

		$url = 'https://graph.facebook.com/';
		switch ($endpoint) {
			case 'header':
				$url .= $connected_account['user_id'] . '?fields=' . $header_fields . '&access_token=' . $access_token;
				break;
			case 'stories':
				$url .= $connected_account['user_id'] . '/stories?fields=media_url,caption,id,media_type,permalink,children%7Bmedia_url,id,media_type,permalink%7D&limit=100&access_token=' . $access_token;
				break;
			case 'recent_hashtag_refetch':
				$url .= $params['hashtag_id'] . '/top_media?user_id=' . $connected_account['user_id'] . '&fields=media_url,media_product_type,id,media_type,permalink&limit=50&access_token=' . $access_token;
				break;
			case 'hashtags_top':
				$url .= $params['hashtag_id'] . '/top_media?user_id=' . $connected_account['user_id'] . '&fields=' . $media_fields . '&limit=' . min($num, 50) . '&access_token=' . $access_token;
				break;
			case 'hashtags_recent':
				$url .= $params['hashtag_id'] . '/recent_media?user_id=' . $connected_account['user_id'] . '&fields=' . $media_fields . '&limit=' . min($num, 50) . '&access_token=' . $access_token;
				break;
			case 'recently_searched_hashtags':
				$url .= $connected_account['user_id'] . '/recently_searched_hashtags?access_token=' . $access_token . '&limit=40';
				break;
			case 'tagged':
				$url .= $connected_account['user_id'] . '/tags?user_id=' . $connected_account['user_id'] . '&fields=' . $media_fields . '&limit=' . $num . '&access_token=' . $access_token . $paging;
				break;
			case 'ig_hashtag_search':
				$url .= 'ig_hashtag_search?user_id=' . $connected_account['user_id'] . '&q=' . urlencode($params['hashtag']) . '&access_token=' . $access_token;
				break;
			case 'comments':
				$url .= $params['post_id'] . '/comments?fields=text,username&access_token=' . $access_token;
				break;
			default:
				$url .= $connected_account['user_id'] . '/media?fields=' . $media_fields_default . '&limit=' . $num . '&access_token=' . $access_token;
				break;
		}

		return $url;
	}
}
