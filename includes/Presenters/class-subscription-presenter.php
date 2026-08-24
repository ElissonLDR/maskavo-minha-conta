<?php
/**
 * DTO seguro de assinaturas (sem IDs internos).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Subscription_Presenter {

	/**
	 * @param int $user_id User ID.
	 * @return array{items: array<int, array<string, string>>, manage_url: string}
	 */
	public static function present( $user_id ) {
		$raw   = Maskavo_MC_Subscription_Service::get_for_user( (int) $user_id );
		$items = array();

		foreach ( $raw as $row ) {
			$status_raw = isset( $row['status_raw'] ) ? (string) $row['status_raw'] : 'active';
			$items[]    = array(
				'label'         => (string) ( $row['label'] ?? '' ),
				'status_label'  => self::status_label( $status_raw ),
				'status_key'    => self::status_key( $status_raw ),
				'since_label'   => self::format_date( (string) ( $row['since_gmt'] ?? '' ) ),
				'duration_label'=> self::duration_label( (string) ( $row['since_gmt'] ?? '' ) ),
				'next_label'    => self::format_date( (string) ( $row['next_gmt'] ?? '' ) ),
			);
		}

		return array(
			'items'      => $items,
			'manage_url' => esc_url( Maskavo_MC_Subscription_Service::manage_url() ),
		);
	}

	/**
	 * @param string $status Status raw.
	 * @return string
	 */
	private static function status_key( $status ) {
		$status = strtolower( $status );
		$map    = array(
			'active'    => 'active',
			'pending'   => 'pending',
			'hold'      => 'hold',
			'cancelled' => 'cancelled',
			'canceled'  => 'cancelled',
			'expired'   => 'expired',
		);
		return isset( $map[ $status ] ) ? $map[ $status ] : 'unknown';
	}

	/**
	 * @param string $status Status raw.
	 * @return string
	 */
	private static function status_label( $status ) {
		$key = self::status_key( $status );
		$labels = array(
			'active'    => __( 'Ativa', 'maskavo-minha-conta' ),
			'pending'   => __( 'Pendente', 'maskavo-minha-conta' ),
			'hold'      => __( 'Em pausa', 'maskavo-minha-conta' ),
			'cancelled' => __( 'Cancelada', 'maskavo-minha-conta' ),
			'expired'   => __( 'Expirada', 'maskavo-minha-conta' ),
			'unknown'   => __( 'Ativa', 'maskavo-minha-conta' ),
		);
		return $labels[ $key ];
	}

	/**
	 * @param string $gmt GMT datetime.
	 * @return string
	 */
	private static function format_date( $gmt ) {
		return Maskavo_MC_Date::format_datetime( $gmt, true );
	}

	/**
	 * @param string $gmt GMT datetime.
	 * @return string
	 */
	private static function duration_label( $gmt ) {
		if ( '' === $gmt || '0000-00-00 00:00:00' === $gmt ) {
			return '';
		}
		$ts = strtotime( $gmt . ' UTC' );
		if ( ! $ts ) {
			return '';
		}
		return human_time_diff( $ts, time() );
	}
}
