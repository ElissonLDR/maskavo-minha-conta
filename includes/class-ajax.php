<?php
/**
 * Endpoints AJAX (perfil + senha).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Ajax {

	/**
	 * Hooks.
	 */
	public static function register() {
		add_action( 'wp_ajax_maskavo_mc_update_profile', array( __CLASS__, 'update_profile' ) );
		add_action( 'wp_ajax_maskavo_mc_change_password', array( __CLASS__, 'change_password' ) );
		add_action( 'wp_ajax_maskavo_mc_update_avatar', array( __CLASS__, 'update_avatar' ) );
		add_action( 'wp_ajax_maskavo_mc_remove_avatar', array( __CLASS__, 'remove_avatar' ) );
	}

	/**
	 * Atualiza dados pessoais.
	 */
	public static function update_profile() {
		Maskavo_MC_Guard::require_ajax_user();

		$user_id = Maskavo_MC_Guard::current_user_id();
		$input   = array(
			'first_name' => isset( $_POST['first_name'] ) ? wp_unslash( $_POST['first_name'] ) : null, // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			'last_name'  => isset( $_POST['last_name'] ) ? wp_unslash( $_POST['last_name'] ) : null, // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		);

		$result = Maskavo_MC_Profile_Service::update( $user_id, $input );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Dados atualizados com sucesso.', 'maskavo-minha-conta' ),
				'profile' => Maskavo_MC_Profile_Presenter::present( $user_id ),
			)
		);
	}

	/**
	 * Altera senha.
	 */
	public static function change_password() {
		Maskavo_MC_Guard::require_ajax_user();

		$user_id = Maskavo_MC_Guard::current_user_id();
		$current = isset( $_POST['current_password'] ) ? (string) wp_unslash( $_POST['current_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$new     = isset( $_POST['new_password'] ) ? (string) wp_unslash( $_POST['new_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$confirm = isset( $_POST['confirm_password'] ) ? (string) wp_unslash( $_POST['confirm_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$result = Maskavo_MC_Password_Service::change( $user_id, $current, $new, $confirm );

		// Limpa strings da memória local (best-effort).
		unset( $current, $new, $confirm );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Senha alterada com sucesso.', 'maskavo-minha-conta' ),
			)
		);
	}

	/**
	 * Upload de foto de perfil.
	 */
	public static function update_avatar() {
		Maskavo_MC_Guard::require_ajax_user();

		$user_id = Maskavo_MC_Guard::current_user_id();
		$file    = isset( $_FILES['avatar'] ) && is_array( $_FILES['avatar'] ) ? $_FILES['avatar'] : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$result = Maskavo_MC_Avatar_Service::upload( $user_id, $file );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Foto atualizada.', 'maskavo-minha-conta' ),
				'profile' => Maskavo_MC_Profile_Presenter::present( $user_id ),
			)
		);
	}

	/**
	 * Remove foto customizada.
	 */
	public static function remove_avatar() {
		Maskavo_MC_Guard::require_ajax_user();

		$user_id = Maskavo_MC_Guard::current_user_id();
		$result  = Maskavo_MC_Avatar_Service::remove( $user_id );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Foto removida.', 'maskavo-minha-conta' ),
				'profile' => Maskavo_MC_Profile_Presenter::present( $user_id ),
			)
		);
	}
}
