<?php
/**
 * Certificados do aluno (Tutor).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Certificate_Service {

	/**
	 * Lista interna de certificados.
	 *
	 * @param int $user_id User ID.
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_for_user( $user_id ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 || ! function_exists( 'tutor_utils' ) ) {
			return array();
		}

		$cache_key = 'maskavo_mc_certs_' . $user_id;
		$cached    = wp_cache_get( $cache_key, 'maskavo_mc' );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$course_ids = tutor_utils()->get_completed_courses_ids_by_user( $user_id );
		$items      = array();

		foreach ( (array) $course_ids as $course_id ) {
			$course_id = (int) $course_id;
			if ( $course_id <= 0 ) {
				continue;
			}

			$completed = tutor_utils()->is_completed_course( $course_id, $user_id );
			if ( ! $completed || empty( $completed->completed_hash ) ) {
				continue;
			}

			$hash    = (string) $completed->completed_hash;
			$cert_id = isset( $completed->comment_ID ) ? (int) $completed->comment_ID : 0;
			$title   = get_the_title( $course_id );
			$url     = self::build_public_url( $hash );
			$image   = self::resolve_image_url( $hash, $cert_id, $course_id );

			$items[] = array(
				'course_title'    => $title ? $title : __( 'Curso', 'maskavo-minha-conta' ),
				'completed_date'  => ! empty( $completed->completion_date ) ? (string) $completed->completion_date : '',
				'certificate_url' => $url,
				'image_url'       => $image,
			);
		}

		wp_cache_set( $cache_key, $items, 'maskavo_mc', 5 * MINUTE_IN_SECONDS );

		return $items;
	}

	/**
	 * URL pública correta do certificado (evita bug do Tutor com DIRECTORY_SEPARATOR no Windows).
	 *
	 * @param string $cert_hash Hash.
	 * @return string
	 */
	public static function build_public_url( $cert_hash ) {
		$cert_hash = sanitize_text_field( (string) $cert_hash );
		if ( '' === $cert_hash || ! function_exists( 'tutor_utils' ) ) {
			return '';
		}

		$page_id = (int) tutor_utils()->get_option( 'tutor_certificate_page' );
		if ( $page_id > 0 ) {
			$permalink = get_permalink( $page_id );
			if ( $permalink ) {
				return (string) add_query_arg( 'cert_hash', $cert_hash, $permalink );
			}
		}

		// Fallback via filtro Tutor (sanitiza barras invertidas).
		$url = (string) apply_filters( 'tutor_certificate_public_url', $cert_hash );
		$url = str_replace( '\\', '/', $url );
		if ( '' === $url || '#' === $url || $url === $cert_hash ) {
			return '';
		}

		return $url;
	}

	/**
	 * Imagem de fundo: JPG gerado, preview do template, ou fallback local.
	 *
	 * @param string $cert_hash Hash.
	 * @param int    $cert_id   comment_ID da conclusão.
	 * @param int    $course_id Course ID.
	 * @return string
	 */
	public static function resolve_image_url( $cert_hash, $cert_id, $course_id ) {
		$cert_hash = sanitize_text_field( (string) $cert_hash );
		$cert_id   = (int) $cert_id;
		$course_id = (int) $course_id;
		$fallback  = MASKAVO_MC_URL . 'assets/img/curriculum-bg.svg';

		$upload = wp_upload_dir();
		if ( empty( $upload['error'] ) && $cert_id > 0 && '' !== $cert_hash ) {
			$rand = (string) get_comment_meta( $cert_id, 'tutor_certificate_has_image', true );
			if ( '' !== $rand ) {
				$rel  = $rand . '-' . $cert_hash . '.jpg';
				$path = trailingslashit( $upload['basedir'] ) . 'tutor-certificates/' . $rel;
				if ( file_exists( $path ) ) {
					return trailingslashit( $upload['baseurl'] ) . 'tutor-certificates/' . $rel;
				}
			}
		}

		$template_key = (string) get_post_meta( $course_id, 'tutor_course_certificate_template', true );
		if ( $template_key && ! in_array( $template_key, array( 'none', 'off', '' ), true ) ) {
			if ( class_exists( '\TUTOR_CERT\Certificate' ) ) {
				try {
					$cert      = new \TUTOR_CERT\Certificate( true );
					$templates = method_exists( $cert, 'get_templates' ) ? $cert->get_templates() : array();
					if ( is_array( $templates ) && ! empty( $templates[ $template_key ]['preview_src'] ) ) {
						return (string) $templates[ $template_key ]['preview_src'];
					}
				} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
					// fallback abaixo.
				}
			}

			return 'https://preview.tutorlms.com/certificate-templates/' . rawurlencode( $template_key ) . '/preview.png';
		}

		return $fallback;
	}
}
