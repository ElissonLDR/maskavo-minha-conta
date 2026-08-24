<?php
/**
 * Bootstrap do plugin.
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Plugin {

	/**
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Init hooks.
	 */
	public function init() {
		Maskavo_MC_Ajax::register();
		Maskavo_MC_Shortcode::register();
		Maskavo_MC_Assets::register();
		Maskavo_MC_Avatar_Service::register();

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Categoria Maskavo (idempotente se CE já registrou).
	 *
	 * @param \Elementor\Elements_Manager $manager Manager.
	 */
	public function register_category( $manager ) {
		if ( ! method_exists( $manager, 'add_category' ) ) {
			return;
		}
		$manager->add_category(
			'maskavo',
			array(
				'title' => __( 'Maskavo', 'maskavo-minha-conta' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}
		$widgets_manager->register( new Maskavo_MC_Account_Widget() );
	}
}
