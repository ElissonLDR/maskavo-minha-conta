<?php
/**
 * Dados de perfil do usuário atual.
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Profile_Service {

	/**
	 * Dados brutos internos (não enviar ao front sem Presenter).
	 *
	 * @param int $user_id User ID.
	 * @return array|WP_Error
	 */
	public static function get( $user_id ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 ) {
			return new WP_Error( 'invalid_user', __( 'Usuário inválido.', 'maskavo-minha-conta' ) );
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return new WP_Error( 'not_found', __( 'Usuário não encontrado.', 'maskavo-minha-conta' ) );
		}

		return array(
			'user_id'      => $user_id,
			'first_name'   => (string) $user->first_name,
			'last_name'    => (string) $user->last_name,
			'display_name' => (string) $user->display_name,
			'email'        => (string) $user->user_email,
			'registered'   => (string) $user->user_registered,
			'avatar_url'   => Maskavo_MC_Avatar_Service::get_url( $user_id, 352 ),
			'has_avatar'   => Maskavo_MC_Avatar_Service::has_custom( $user_id ),
		);
	}

	/**
	 * Valida nome (uma palavra, sem números).
	 *
	 * @param string $value Valor.
	 * @return true|WP_Error
	 */
	public static function validate_first_name( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return new WP_Error( 'first_name_empty', __( 'Informe o nome.', 'maskavo-minha-conta' ) );
		}
		if ( preg_match( '/\d/u', $value ) ) {
			return new WP_Error( 'first_name_numbers', __( 'O nome não pode conter números.', 'maskavo-minha-conta' ) );
		}
		if ( preg_match( '/\s/u', $value ) ) {
			return new WP_Error( 'first_name_words', __( 'O nome deve ter apenas uma palavra.', 'maskavo-minha-conta' ) );
		}
		if ( ! preg_match( "/^[\p{L}'’-]+$/u", $value ) ) {
			return new WP_Error( 'first_name_invalid', __( 'O nome contém caracteres inválidos.', 'maskavo-minha-conta' ) );
		}
		return true;
	}

	/**
	 * Valida sobrenome (uma ou mais palavras, sem números).
	 *
	 * @param string $value Valor.
	 * @return true|WP_Error
	 */
	public static function validate_last_name( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return new WP_Error( 'last_name_empty', __( 'Informe o sobrenome.', 'maskavo-minha-conta' ) );
		}
		if ( preg_match( '/\d/u', $value ) ) {
			return new WP_Error( 'last_name_numbers', __( 'O sobrenome não pode conter números.', 'maskavo-minha-conta' ) );
		}
		if ( ! preg_match( "/^[\p{L}'’\-\s]+$/u", $value ) ) {
			return new WP_Error( 'last_name_invalid', __( 'O sobrenome contém caracteres inválidos.', 'maskavo-minha-conta' ) );
		}
		return true;
	}

	/**
	 * Atualiza campos permitidos do próprio usuário.
	 *
	 * @param int   $user_id User ID.
	 * @param array $input   Input sanitizado.
	 * @return true|WP_Error
	 */
	public static function update( $user_id, array $input ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 || $user_id !== Maskavo_MC_Guard::current_user_id() ) {
			return new WP_Error( 'forbidden', __( 'Não autorizado.', 'maskavo-minha-conta' ) );
		}

		$first = isset( $input['first_name'] ) ? sanitize_text_field( $input['first_name'] ) : null;
		$last  = isset( $input['last_name'] ) ? sanitize_text_field( $input['last_name'] ) : null;

		if ( null !== $first ) {
			$ok = self::validate_first_name( $first );
			if ( is_wp_error( $ok ) ) {
				return $ok;
			}
			$first = trim( $first );
		}
		if ( null !== $last ) {
			$ok = self::validate_last_name( $last );
			if ( is_wp_error( $ok ) ) {
				return $ok;
			}
			$last = preg_replace( '/\s+/u', ' ', trim( $last ) );
		}

		$userdata = array( 'ID' => $user_id );

		if ( null !== $first ) {
			$userdata['first_name'] = $first;
		}
		if ( null !== $last ) {
			$userdata['last_name'] = $last;
		}

		if ( null !== $first || null !== $last ) {
			$fn = null !== $first ? $first : (string) get_user_meta( $user_id, 'first_name', true );
			$ln = null !== $last ? $last : (string) get_user_meta( $user_id, 'last_name', true );
			$dn = trim( $fn . ' ' . $ln );
			if ( '' !== $dn ) {
				$userdata['display_name'] = $dn;
			}
		}

		$result = wp_update_user( $userdata );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		wp_cache_delete( 'maskavo_mc_profile_' . $user_id, 'maskavo_mc' );

		return true;
	}
}
