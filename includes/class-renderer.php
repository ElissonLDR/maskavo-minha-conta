<?php
/**
 * Renderização do shell Minha Conta.
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Renderer {

	/**
	 * @param array $args Args (sections on/off, title).
	 * @return string
	 */
	public static function render( array $args = array() ) {
		$defaults = array(
			'title'             => __( 'Minha conta', 'maskavo-minha-conta' ),
			'show_profile'      => true,
			'show_subscription' => true,
			'show_certificates' => true,
			'show_reviews'      => true,
			'show_security'     => true,
			'show_meus_cursos'  => true,
		);
		$args = wp_parse_args( $args, $defaults );

		Maskavo_MC_Assets::enqueue();

		if ( ! Maskavo_MC_Guard::is_logged_in() ) {
			return self::render_guest( $args );
		}

		$user_id = Maskavo_MC_Guard::current_user_id();

		$profile       = Maskavo_MC_Profile_Presenter::present( $user_id );
		$subscriptions = Maskavo_MC_Subscription_Presenter::present( $user_id );
		$certificates  = Maskavo_MC_Certificate_Presenter::present( $user_id );
		$reviews       = Maskavo_MC_Review_Presenter::present( $user_id );

		$ctx = array(
			'args'          => $args,
			'profile'       => $profile,
			'subscriptions' => $subscriptions,
			'certificates'  => $certificates,
			'reviews'       => $reviews,
			'meus_cursos'   => Maskavo_MC_Guard::meus_cursos_url(),
			'logout_url'    => Maskavo_MC_Guard::logout_url(),
		);

		ob_start();
		self::view( 'shell', $ctx );
		return (string) ob_get_clean();
	}

	/**
	 * @param array $args Args.
	 * @return string
	 */
	private static function render_guest( array $args ) {
		$login = Maskavo_MC_Guard::login_url();
		ob_start();
		?>
		<div class="maskavo-mc maskavo-mc--guest">
			<div class="maskavo-mc-card maskavo-mc-card--guest">
				<h2 class="maskavo-mc-guest__title"><?php echo esc_html( $args['title'] ); ?></h2>
				<p class="maskavo-mc-guest__text"><?php esc_html_e( 'Faça login para ver e editar sua conta.', 'maskavo-minha-conta' ); ?></p>
				<a class="maskavo-mc-btn maskavo-mc-btn--primary" href="<?php echo esc_url( $login ); ?>">
					<?php esc_html_e( 'Entrar', 'maskavo-minha-conta' ); ?>
				</a>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param string               $name Template name (sem .php).
	 * @param array<string, mixed> $ctx  Context.
	 */
	public static function view( $name, array $ctx = array() ) {
		$name = preg_replace( '/[^a-z0-9\-_]/', '', strtolower( (string) $name ) );
		$file = MASKAVO_MC_PATH . 'includes/Views/' . $name . '.php';
		if ( ! is_readable( $file ) ) {
			return;
		}
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- escopo controlado de views.
		extract( $ctx, EXTR_SKIP );
		include $file;
	}
}
