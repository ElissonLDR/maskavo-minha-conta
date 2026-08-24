<?php
/**
 * Avatar customizado do usuário (upload + filtro get_avatar).
 *
 * @package Maskavo_Minha_Conta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maskavo_MC_Avatar_Service {

	const META_KEY = 'maskavo_mc_avatar_id';
	const TUTOR_META_KEY = '_tutor_profile_photo';
	const MAX_BYTES = 2097152; // 2 MB

	/**
	 * Hooks (filtro de avatar site-wide para quem tem foto Maskavo).
	 */
	public static function register() {
		add_filter( 'pre_get_avatar_data', array( __CLASS__, 'filter_avatar_data' ), 20, 2 );
	}

	/**
	 * @return string
	 */
	public static function meta_key() {
		return (string) apply_filters( 'maskavo_mc_avatar_meta_key', self::META_KEY );
	}

	/**
	 * Meta do Tutor LMS (foto já existente no site).
	 *
	 * @return string
	 */
	public static function tutor_meta_key() {
		return (string) apply_filters( 'maskavo_mc_tutor_avatar_meta_key', self::TUTOR_META_KEY );
	}

	/**
	 * ID do attachment Maskavo.
	 *
	 * @param int $user_id User ID.
	 * @return int
	 */
	public static function get_attachment_id( $user_id ) {
		return (int) get_user_meta( (int) $user_id, self::meta_key(), true );
	}

	/**
	 * ID do attachment do Tutor.
	 *
	 * @param int $user_id User ID.
	 * @return int
	 */
	public static function get_tutor_attachment_id( $user_id ) {
		return (int) get_user_meta( (int) $user_id, self::tutor_meta_key(), true );
	}

	/**
	 * Attachment efetivo: Maskavo → Tutor.
	 *
	 * @param int $user_id User ID.
	 * @return int
	 */
	public static function get_effective_attachment_id( $user_id ) {
		$aid = self::get_attachment_id( $user_id );
		if ( $aid > 0 && wp_attachment_is_image( $aid ) ) {
			return $aid;
		}
		$tutor = self::get_tutor_attachment_id( $user_id );
		if ( $tutor > 0 && wp_attachment_is_image( $tutor ) ) {
			return $tutor;
		}
		return 0;
	}

	/**
	 * URL do avatar padrão (sem foto).
	 *
	 * @return string
	 */
	public static function default_url() {
		return (string) apply_filters(
			'maskavo_mc_default_avatar_url',
			MASKAVO_MC_URL . 'assets/img/avatar-default.svg'
		);
	}

	/**
	 * URL do avatar (Maskavo → Tutor → get_avatar → placeholder).
	 *
	 * @param int $user_id User ID.
	 * @param int $size    Size.
	 * @return string
	 */
	public static function get_url( $user_id, $size = 192 ) {
		$user_id = (int) $user_id;
		$aid     = self::get_effective_attachment_id( $user_id );
		if ( $aid > 0 ) {
			$url = wp_get_attachment_image_url( $aid, array( (int) $size, (int) $size ) );
			if ( ! $url ) {
				$url = wp_get_attachment_url( $aid );
			}
			if ( $url ) {
				return (string) $url;
			}
		}

		$avatar = (string) get_avatar_url( $user_id, array( 'size' => (int) $size ) );
		if ( '' !== $avatar ) {
			return $avatar;
		}

		return self::default_url();
	}

	/**
	 * Tem foto própria (Maskavo ou Tutor).
	 *
	 * @param int $user_id User ID.
	 * @return bool
	 */
	public static function has_custom( $user_id ) {
		return self::get_effective_attachment_id( $user_id ) > 0;
	}

	/**
	 * Força avatar custom no WP / Tutor / etc.
	 *
	 * @param array $args        Avatar args.
	 * @param mixed $id_or_email ID ou e-mail.
	 * @return array
	 */
	public static function filter_avatar_data( $args, $id_or_email ) {
		$user_id = self::resolve_user_id( $id_or_email );
		if ( $user_id <= 0 ) {
			return $args;
		}

		$aid = self::get_effective_attachment_id( $user_id );
		if ( $aid <= 0 ) {
			return $args;
		}

		$size = isset( $args['size'] ) ? (int) $args['size'] : 96;
		$url  = wp_get_attachment_image_url( $aid, array( $size, $size ) );
		if ( ! $url ) {
			$url = wp_get_attachment_url( $aid );
		}
		if ( ! $url ) {
			return $args;
		}

		$args['url']          = $url;
		$args['found_avatar'] = true;
		return $args;
	}

	/**
	 * Sincroniza meta Maskavo + Tutor.
	 *
	 * @param int $user_id User ID.
	 * @param int $attach_id Attachment ID (0 = limpar).
	 */
	private static function sync_metas( $user_id, $attach_id ) {
		$user_id   = (int) $user_id;
		$attach_id = (int) $attach_id;
		if ( $attach_id > 0 ) {
			update_user_meta( $user_id, self::meta_key(), $attach_id );
			update_user_meta( $user_id, self::tutor_meta_key(), $attach_id );
			return;
		}
		delete_user_meta( $user_id, self::meta_key() );
		delete_user_meta( $user_id, self::tutor_meta_key() );
	}

	/**
	 * Upload e define avatar do usuário atual.
	 *
	 * @param int   $user_id User ID.
	 * @param array $file    $_FILES['avatar'] item.
	 * @return array|WP_Error { attachment_id, url }
	 */
	public static function upload( $user_id, array $file ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 || $user_id !== Maskavo_MC_Guard::current_user_id() ) {
			return new WP_Error( 'forbidden', __( 'Não autorizado.', 'maskavo-minha-conta' ) );
		}

		if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'no_file', __( 'Selecione uma imagem.', 'maskavo-minha-conta' ) );
		}

		if ( ! empty( $file['error'] ) ) {
			return new WP_Error( 'upload_error', __( 'Falha no upload. Tente outra imagem.', 'maskavo-minha-conta' ) );
		}

		$size = isset( $file['size'] ) ? (int) $file['size'] : 0;
		if ( $size <= 0 || $size > self::MAX_BYTES ) {
			return new WP_Error( 'too_large', __( 'A imagem deve ter no máximo 2 MB.', 'maskavo-minha-conta' ) );
		}

		$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
		$allowed = array( 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp' );
		$ext     = isset( $check['ext'] ) ? strtolower( (string) $check['ext'] ) : '';
		$type    = isset( $check['type'] ) ? (string) $check['type'] : '';

		if ( ! $ext || ! isset( $allowed[ $ext ] ) || $type !== $allowed[ $ext ] ) {
			return new WP_Error( 'invalid_type', __( 'Use JPG, PNG, WEBP ou GIF.', 'maskavo-minha-conta' ) );
		}

		// Confirma que é imagem real.
		$image_info = @getimagesize( $file['tmp_name'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( false === $image_info ) {
			return new WP_Error( 'invalid_image', __( 'Arquivo de imagem inválido.', 'maskavo-minha-conta' ) );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$overrides = array(
			'test_form' => false,
			'mimes'     => $allowed,
		);

		$moved = wp_handle_upload( $file, $overrides );
		if ( isset( $moved['error'] ) ) {
			return new WP_Error( 'handle_error', (string) $moved['error'] );
		}

		$attachment = array(
			'post_mime_type' => $moved['type'],
			'post_title'     => sanitize_file_name( wp_basename( $moved['file'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_author'    => $user_id,
		);

		$attach_id = wp_insert_attachment( $attachment, $moved['file'] );
		if ( is_wp_error( $attach_id ) || ! $attach_id ) {
			return new WP_Error( 'attach_error', __( 'Não foi possível salvar a imagem.', 'maskavo-minha-conta' ) );
		}

		$meta = wp_generate_attachment_metadata( $attach_id, $moved['file'] );
		wp_update_attachment_metadata( $attach_id, $meta );

		$old_maskavo = self::get_attachment_id( $user_id );
		$old_tutor   = self::get_tutor_attachment_id( $user_id );
		self::sync_metas( $user_id, (int) $attach_id );

		if ( $old_maskavo > 0 && $old_maskavo !== (int) $attach_id ) {
			self::maybe_delete_attachment( $old_maskavo, $user_id );
		}
		if ( $old_tutor > 0 && $old_tutor !== (int) $attach_id && $old_tutor !== $old_maskavo ) {
			self::maybe_delete_attachment( $old_tutor, $user_id );
		}

		wp_cache_delete( 'maskavo_mc_profile_' . $user_id, 'maskavo_mc' );

		$url = wp_get_attachment_image_url( $attach_id, array( 192, 192 ) );
		if ( ! $url ) {
			$url = wp_get_attachment_url( $attach_id );
		}

		return array(
			'attachment_id' => (int) $attach_id,
			'url'           => (string) $url,
		);
	}

	/**
	 * Remove avatar customizado (Maskavo + Tutor).
	 *
	 * @param int $user_id User ID.
	 * @return true|WP_Error
	 */
	public static function remove( $user_id ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 || $user_id !== Maskavo_MC_Guard::current_user_id() ) {
			return new WP_Error( 'forbidden', __( 'Não autorizado.', 'maskavo-minha-conta' ) );
		}

		$old_maskavo = self::get_attachment_id( $user_id );
		$old_tutor   = self::get_tutor_attachment_id( $user_id );
		self::sync_metas( $user_id, 0 );
		clean_user_cache( $user_id );

		if ( $old_maskavo > 0 ) {
			self::maybe_delete_attachment( $old_maskavo, $user_id );
		}
		if ( $old_tutor > 0 && $old_tutor !== $old_maskavo ) {
			self::maybe_delete_attachment( $old_tutor, $user_id );
		}

		wp_cache_delete( 'maskavo_mc_profile_' . $user_id, 'maskavo_mc' );
		return true;
	}

	/**
	 * Concede cap temporária para apagar o próprio avatar no front.
	 *
	 * @param array   $allcaps All caps.
	 * @param array   $caps    Required caps.
	 * @param array   $args    Args (0=cap, 2=object id).
	 * @param WP_User $user    User.
	 * @return array
	 */
	public static function grant_delete_avatar_cap( $allcaps, $caps, $args, $user ) {
		if ( empty( $GLOBALS['maskavo_mc_deleting_avatar_id'] ) ) {
			return $allcaps;
		}
		if ( empty( $args[0] ) || 'delete_post' !== $args[0] ) {
			return $allcaps;
		}
		$post_id = isset( $args[2] ) ? (int) $args[2] : 0;
		if ( $post_id === (int) $GLOBALS['maskavo_mc_deleting_avatar_id'] ) {
			$allcaps['delete_post']  = true;
			$allcaps['delete_posts'] = true;
		}
		return $allcaps;
	}

	/**
	 * Apaga attachment do avatar (mesmo sem cap delete_post no front).
	 *
	 * @param int $attachment_id Attachment ID.
	 * @param int $user_id       User ID.
	 */
	private static function maybe_delete_attachment( $attachment_id, $user_id ) {
		$attachment_id = (int) $attachment_id;
		$user_id       = (int) $user_id;
		$post          = get_post( $attachment_id );
		if ( ! $post || 'attachment' !== $post->post_type ) {
			return;
		}

		// Não apaga se outro usuário ainda usa o mesmo attachment como avatar.
		$others_maskavo = get_users(
			array(
				'meta_key'   => self::meta_key(),
				'meta_value' => (string) $attachment_id,
				'exclude'    => array( $user_id ),
				'fields'     => 'ID',
				'number'     => 1,
			)
		);
		$others_tutor = get_users(
			array(
				'meta_key'   => self::tutor_meta_key(),
				'meta_value' => (string) $attachment_id,
				'exclude'    => array( $user_id ),
				'fields'     => 'ID',
				'number'     => 1,
			)
		);
		if ( ! empty( $others_maskavo ) || ! empty( $others_tutor ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/post.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$GLOBALS['maskavo_mc_deleting_avatar_id'] = $attachment_id;
		add_filter( 'user_has_cap', array( __CLASS__, 'grant_delete_avatar_cap' ), 10, 4 );
		$deleted = wp_delete_attachment( $attachment_id, true );
		remove_filter( 'user_has_cap', array( __CLASS__, 'grant_delete_avatar_cap' ), 10 );
		unset( $GLOBALS['maskavo_mc_deleting_avatar_id'] );

		// Fallback se wp_delete_attachment falhar por qualquer motivo.
		if ( ! $deleted && get_post( $attachment_id ) ) {
			$file = get_attached_file( $attachment_id );
			wp_delete_post( $attachment_id, true );
			if ( $file && file_exists( $file ) ) {
				wp_delete_file( $file );
			}
		}
	}

	/**
	 * @param mixed $id_or_email ID ou e-mail.
	 * @return int
	 */
	private static function resolve_user_id( $id_or_email ) {
		if ( is_numeric( $id_or_email ) ) {
			return (int) $id_or_email;
		}
		if ( $id_or_email instanceof WP_User ) {
			return (int) $id_or_email->ID;
		}
		if ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
			return (int) $id_or_email->user_id;
		}
		if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
			return $user ? (int) $user->ID : 0;
		}
		return 0;
	}
}
