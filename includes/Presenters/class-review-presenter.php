<?php
/**
 * DTO seguro de avaliações.
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Review_Presenter {

	/**
	 * @param int $user_id User ID.
	 * @return array<int, array<string, mixed>>
	 */
	public static function present( $user_id ) {
		$raw   = Maskavo_MC_Review_Service::get_for_user( (int) $user_id );
		$items = array();

		foreach ( $raw as $row ) {
			$title = (string) ( $row['course_title'] ?? '' );
			if ( '' === $title ) {
				$title = __( 'Curso', 'maskavo-minha-conta' );
			}

			$date = '';
			if ( ! empty( $row['date'] ) ) {
				$date = Maskavo_MC_Date::format_datetime( (string) $row['date'], false );
			}

			$content = isset( $row['content'] ) ? wp_strip_all_tags( (string) $row['content'] ) : '';
			$content = wp_html_excerpt( $content, 280, '…' );

			$items[] = array(
				'title'        => $title,
				'rating'       => (int) ( $row['rating'] ?? 0 ),
				'content'      => $content,
				'date_label'   => $date,
				'status_key'   => self::status_key( (string) ( $row['status'] ?? '' ) ),
				'status_label' => self::status_label( (string) ( $row['status'] ?? '' ) ),
				'url'          => ! empty( $row['course_url'] ) ? esc_url( (string) $row['course_url'] ) : '',
			);
		}

		return $items;
	}

	/**
	 * @param string $status Status.
	 * @return string
	 */
	private static function status_key( $status ) {
		$status = strtolower( $status );
		if ( in_array( $status, array( 'approved', 'hold', 'pending' ), true ) ) {
			return $status;
		}
		return 'approved';
	}

	/**
	 * @param string $status Status.
	 * @return string
	 */
	private static function status_label( $status ) {
		$key = self::status_key( $status );
		$map = array(
			'approved' => __( 'Publicada', 'maskavo-minha-conta' ),
			'hold'     => __( 'Em análise', 'maskavo-minha-conta' ),
			'pending'  => __( 'Pendente', 'maskavo-minha-conta' ),
		);
		return $map[ $key ];
	}
}
