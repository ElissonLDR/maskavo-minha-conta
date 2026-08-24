<?php
/**
 * Plugin Name: Maskavo Minha Conta
 * Description: Área Minha Conta — dados pessoais, assinatura, certificados e senha (widget Elementor + shortcode).
 * Version: 1.0.21
 * Author: Elisson Rodrigues
 * Requires Plugins: elementor
 * Text Domain: maskavo-minha-conta
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MASKAVO_MC_VERSION', '1.0.21' );
define( 'MASKAVO_MC_FILE', __FILE__ );
define( 'MASKAVO_MC_PATH', plugin_dir_path( __FILE__ ) );
define( 'MASKAVO_MC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoload simples das classes do plugin.
 *
 * @param string $class Class name.
 */
spl_autoload_register(
	static function ( $class ) {
		if ( 0 !== strpos( $class, 'Maskavo_MC_' ) ) {
			return;
		}

		$map = array(
			'Maskavo_MC_Icons'                    => 'includes/class-icons.php',
			'Maskavo_MC_Date'                     => 'includes/class-date.php',
			'Maskavo_MC_Plugin'                  => 'includes/class-plugin.php',
			'Maskavo_MC_Assets'                  => 'includes/class-assets.php',
			'Maskavo_MC_Shortcode'               => 'includes/class-shortcode.php',
			'Maskavo_MC_Ajax'                    => 'includes/class-ajax.php',
			'Maskavo_MC_Renderer'                => 'includes/class-renderer.php',
			'Maskavo_MC_Guard'                   => 'includes/Security/class-guard.php',
			'Maskavo_MC_Profile_Service'         => 'includes/Domain/class-profile-service.php',
			'Maskavo_MC_Avatar_Service'          => 'includes/Domain/class-avatar-service.php',
			'Maskavo_MC_Subscription_Service'    => 'includes/Domain/class-subscription-service.php',
			'Maskavo_MC_Certificate_Service'     => 'includes/Domain/class-certificate-service.php',
			'Maskavo_MC_Review_Service'          => 'includes/Domain/class-review-service.php',
			'Maskavo_MC_Password_Service'        => 'includes/Domain/class-password-service.php',
			'Maskavo_MC_Profile_Presenter'       => 'includes/Presenters/class-profile-presenter.php',
			'Maskavo_MC_Subscription_Presenter'  => 'includes/Presenters/class-subscription-presenter.php',
			'Maskavo_MC_Certificate_Presenter'   => 'includes/Presenters/class-certificate-presenter.php',
			'Maskavo_MC_Review_Presenter'        => 'includes/Presenters/class-review-presenter.php',
			'Maskavo_MC_Account_Widget'          => 'includes/widgets/class-account-widget.php',
		);

		if ( empty( $map[ $class ] ) ) {
			return;
		}

		$file = MASKAVO_MC_PATH . $map[ $class ];
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
);

add_action(
	'plugins_loaded',
	static function () {
		Maskavo_MC_Plugin::instance()->init();
	},
	20
);
