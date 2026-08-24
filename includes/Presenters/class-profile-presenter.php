<?php
/**
 * DTO seguro de perfil (whitelist).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Profile_Presenter {

	/**
	 * @param int $user_id User ID.
	 * @return array<string, string>
	 */
	public static function present( $user_id ) {
		$data = Maskavo_MC_Profile_Service::get( (int) $user_id );
		if ( is_wp_error( $data ) || ! is_array( $data ) ) {
			return array(
				'display_name' => '',
				'first_name'   => '',
				'last_name'    => '',
				'email'        => '',
				'member_since' => '',
				'avatar_url'   => '',
				'has_avatar'   => false,
			);
		}

		$registered = ! empty( $data['registered'] ) ? $data['registered'] : '';
		$since      = $registered ? Maskavo_MC_Date::format_datetime( $registered, true ) : '';

		return array(
			'display_name' => (string) $data['display_name'],
			'first_name'   => (string) $data['first_name'],
			'last_name'    => (string) $data['last_name'],
			'email'        => (string) $data['email'],
			'member_since' => $since,
			'avatar_url'   => esc_url( (string) $data['avatar_url'] ),
			'has_avatar'   => ! empty( $data['has_avatar'] ),
		);
	}
}
