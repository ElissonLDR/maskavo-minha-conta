<?php
/**
 * Avaliações de cursos feitas pelo aluno (Tutor).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Review_Service {

	/**
	 * Reviews internas (filtrar no Presenter).
	 *
	 * @param int $user_id User ID.
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_for_user( $user_id ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 || ! function_exists( 'tutor_utils' ) ) {
			return array();
		}

		$cache_key = 'maskavo_mc_reviews_' . $user_id;
		$cached    = wp_cache_get( $cache_key, 'maskavo_mc' );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$raw = tutor_utils()->get_reviews_by_user(
			$user_id,
			0,
			50,
			false,
			null,
			array( 'approved', 'hold', 'pending' )
		);

		$items = array();
		foreach ( (array) $raw as $row ) {
			if ( ! is_object( $row ) ) {
				continue;
			}

			$course_id = isset( $row->comment_post_ID ) ? (int) $row->comment_post_ID : 0;
			$rating    = isset( $row->rating ) ? (float) $row->rating : 0;

			$items[] = array(
				'course_id'    => $course_id,
				'course_title' => isset( $row->course_title ) ? (string) $row->course_title : '',
				'rating'       => max( 0, min( 5, (int) round( $rating ) ) ),
				'content'      => isset( $row->comment_content ) ? (string) $row->comment_content : '',
				'date'         => isset( $row->comment_date ) ? (string) $row->comment_date : '',
				'status'       => isset( $row->comment_status ) ? (string) $row->comment_status : 'approved',
				'course_url'   => $course_id > 0 ? (string) get_permalink( $course_id ) : '',
			);
		}

		wp_cache_set( $cache_key, $items, 'maskavo_mc', 5 * MINUTE_IN_SECONDS );

		return $items;
	}
}
