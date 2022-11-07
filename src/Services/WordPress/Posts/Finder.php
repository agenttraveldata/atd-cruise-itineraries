<?php

namespace ATD\CruiseFactory\Services\WordPress\Posts;

use ATD\CruiseFactory\Feed;
use ATD\CruiseFactory\Post;
use ATD\CruiseFactory\Services\ConvertClass;
use WP_Post;
use WP_Query;

class Finder {
	public static function getDepartureByIdAndType( int $departureId, string $type ): ?object {
		switch ( $type ) {
			case 'special':
				$feed = new Feed\SpecialDeparture();
				break;
			case 'cruise':
				$feed = new Feed\Departure();
				break;
		}

		try {
			if ( ! empty( $feed ) && ( $departure = $feed->getEntityManager()->getMapper( $feed->getEntity() )->find( $departureId ) ) ) {
				return $departure;
			}
		} catch ( \Exception ) {
		}

		return null;
	}

	public static function getPostByPostTypeAndId( string $postType, int $id, bool $returnQuery = false ): false|WP_Post|WP_Query {
		if ( $postClass = Post\Provider::getPostClassByPostType( $postType ) ) {
			$feedClass = ConvertClass::toFeedFromPost( $postClass );

			if ( class_exists( $feedClass ) ) {
				/**
				 * @var Post\Post $postClass
				 * @var Feed\Feed $feedClass
				 */
				$query = new WP_Query( [
					'post_type'      => $postClass::$postType,
					'nopaging'       => true,
					'no_found_rows'  => true,
					'posts_per_page' => 1,
					'post_status'    => 'publish',
					'meta_key'       => $feedClass::$metaKeyId,
					'meta_value'     => $id
				] );

				if ( $query->post_count === 1 ) {
					if ( $returnQuery ) {
						return $query;
					}

					return $query->post ?? false;
				}
			}
		}

		return false;
	}

	public static function getPostByFeedAndId( string $feedClass, int $id ): false|WP_Post {
		$postClass = ConvertClass::toPostFromFeed( $feedClass );

		if ( class_exists( $feedClass ) && class_exists( $postClass ) ) {
			/**
			 * @var Post\Post $postClass
			 * @var Feed\Feed $feedClass
			 */
			$query = new WP_Query( [
				'post_type'      => $postClass::$postType,
				'nopaging'       => true,
				'no_found_rows'  => true,
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'meta_key'       => $feedClass::$metaKeyId,
				'meta_value'     => $id
			] );

			if ( $query->post_count === 1 ) {
				return $query->post ?? false;
			}
		}

		return false;
	}

	public static function getQueryByPostTypeAndMetaValues( string $postType, array $metaValues ): WP_Query {
		$queryParams = [];

		/** @var Post\Post $postClass */
		if ( $postClass = Post\Provider::getPostClassByPostType( $postType ) ) {
			/** @var Feed\Feed $feedClass */
			$feedClass = ConvertClass::toFeedFromPost( $postClass );

			$queryParams = [
				'post_type'  => $postClass::$postType,
				'meta_query' => [
					[
						'key'     => $feedClass::$metaKeyId,
						'value'   => $metaValues,
						'compare' => 'IN'
					]
				]
			];
		}

		return new WP_Query( $queryParams );
	}

	public static function getPostAttachments( ?int $postId = null, ?string $imageType = null ): array {
		$images = [];

		if ( ! $postId ) {
			$postId = get_the_ID();
		}

		if ( $attachedMedia = get_attached_media( $imageType ?? 'image', $postId ) ) {
			foreach ( $attachedMedia as $image ) {
				if ( $rawMetaData = get_metadata_raw( 'post', $image->ID ) ) {
					foreach ( $rawMetaData as $key => $datum ) {
						if ( str_starts_with( $key, 'atd_cf' ) ) {
							$metaData[ $key ] = reset( $datum );
						}
					}
				}

				if ( ! empty( $metaData ) ) {
					/*
					 * We really only want to return attachments we added
					 * because we know how to work with them
					 */
					$details = [
						'id'          => $image->ID,
						'name'        => $image->post_title,
						'description' => $image->post_content
					];

					if ( isset( $metaData[ Feed\Cabin::$metaKeyId ] ) ) {
						$type = $metaData['atd_cfi_cabin_image_type'];

						if ( false !== ( $key = array_search( $metaData[ Feed\Cabin::$metaKeyId ], array_column( $images, 'meta' ) ) ) ) {
							$images[ $key ][ $type ] = $image->ID;
							continue;
						}

						$details['meta']  = $metaData[ Feed\Cabin::$metaKeyId ];
						$details['type']  = 'cabin';
						$details[ $type ] = $image->ID;
					} elseif ( isset( $metaData[ Feed\Deck::$metaKeyId ] ) ) {
						$details['meta']  = $metaData[ Feed\Deck::$metaKeyId ];
						$details['type']  = 'deck';
						$details['image'] = wp_get_attachment_image_url( $image->ID );
					} else {
						$details['meta']  = $metaData;
						$details['type']  = 'general';
						$details['image'] = wp_get_attachment_image_url( $image->ID );
					}

					$images[] = $details;
				}
			}
		}

		return $images;
	}

	public function sortAttachedMediaQuery( array $args, string $type, WP_Post $post ): array {
		if ( $post->post_type === Post\Ship::$postType ) {
			$args['orderby'] = 'post_title';

			switch ( $type ) {
				case 'cabin':
					$args['post_mime_type'] = 'image';
					$args['meta_key']       = 'atd_cfi_cabin_order';
					$args['orderby']        = 'meta_value';
					$args['meta_query']     = [
						[
							'key'     => Feed\Cabin::$metaKeyId,
							'compare' => 'EXISTS'
						]
					];
					break;
				case 'deck':
					$args['post_mime_type'] = 'image';
					$args['meta_query']     = [
						[
							'key'     => Feed\Deck::$metaKeyId,
							'compare' => 'EXISTS'
						]
					];
					break;
			}
		}

		return $args;
	}
}