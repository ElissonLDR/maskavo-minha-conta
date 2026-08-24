<?php
/**
 * Guardas de autenticação e autorização.
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Guard {

	const NONCE_ACTION = 'maskavo_mc_account';

	/**
	 * Usuário autenticado?
	 *
	 * @return bool
	 */
	public static function is_logged_in() {
		return is_user_logged_in();
	}

	/**
	 * ID do usuário atual (nunca confiar em user_id do request).
	 *
	 * @return int
	 */
	public static function current_user_id() {
		return (int) get_current_user_id();
	}

	/**
	 * Valida nonce AJAX.
	 *
	 * @return bool
	 */
	public static function verify_ajax_nonce() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		return (bool) wp_verify_nonce( $nonce, self::NONCE_ACTION );
	}

	/**
	 * Exige login + nonce; responde JSON e encerra se falhar.
	 */
	public static function require_ajax_user() {
		if ( ! self::is_logged_in() ) {
			wp_send_json_error(
				array( 'message' => __( 'Você precisa estar logado.', 'maskavo-minha-conta' ) ),
				401
			);
		}

		if ( ! self::verify_ajax_nonce() ) {
			wp_send_json_error(
				array( 'message' => __( 'Sessão inválida. Recarregue a página.', 'maskavo-minha-conta' ) ),
				403
			);
		}

		// Rate limit simples por usuário (evita brute-force de senha).
		$user_id = self::current_user_id();
		$key     = 'maskavo_mc_rl_' . $user_id;
		$hits    = (int) get_transient( $key );
		if ( $hits >= 30 ) {
			wp_send_json_error(
				array( 'message' => __( 'Muitas tentativas. Aguarde um minuto.', 'maskavo-minha-conta' ) ),
				429
			);
		}
		set_transient( $key, $hits + 1, MINUTE_IN_SECONDS );
	}

	/**
	 * URL de login front.
	 *
	 * @return string
	 */
	public static function login_url() {
		return (string) apply_filters( 'maskavo_mc_login_url', home_url( '/login/' ) );
	}

	/**
	 * URL Meus cursos.
	 *
	 * @return string
	 */
	public static function meus_cursos_url() {
		if ( class_exists( 'Maskavo_CE_Access' ) && method_exists( 'Maskavo_CE_Access', 'meus_cursos_url' ) ) {
			return Maskavo_CE_Access::meus_cursos_url();
		}
		return (string) apply_filters( 'maskavo_mc_meus_cursos_url', home_url( '/meu-cursos/' ) );
	}

	/**
	 * URL logout front.
	 *
	 * @return string
	 */
	public static function logout_url() {
		if ( class_exists( 'Maskavo_CE_Dashboard' ) && method_exists( 'Maskavo_CE_Dashboard', 'logout_url' ) ) {
			return Maskavo_CE_Dashboard::logout_url();
		}
		return wp_nonce_url( home_url( '/sair/' ), 'maskavo_ce_logout' );
	}
}
