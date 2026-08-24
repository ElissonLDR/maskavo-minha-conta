<?php
/**
 * DTO seguro de certificados.
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Certificate_Presenter {

	/**
	 * @param int $user_id User ID.
	 * @return array<int, array<string, string>>
	 */
	public static function present( $user_id ) {
		$raw   = Maskavo_MC_Certificate_Service::get_for_user( (int) $user_id );
		$items = array();

		foreach ( $raw as $row ) {
			$date = '';
			if ( ! empty( $row['completed_date'] ) ) {
				$date = Maskavo_MC_Date::format_datetime( (string) $row['completed_date'], false );
			}

			$url   = ! empty( $row['certificate_url'] ) ? esc_url( (string) $row['certificate_url'] ) : '';
			$image = ! empty( $row['image_url'] ) ? esc_url( (string) $row['image_url'] ) : esc_url( MASKAVO_MC_URL . 'assets/img/curriculum-bg.svg' );

			$items[] = array(
				'title'           => (string) ( $row['course_title'] ?? '' ),
				'completed_label' => $date,
				'url'             => $url,
				'image_url'       => $image,
			);
		}

		return $items;
	}
}
