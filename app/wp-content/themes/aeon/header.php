<?php
/**
 * Header.
 *
 * @package AEON
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php aeon_e( 'skip_to_content' ); ?></a>

<div class="aeon-progress" aria-hidden="true"><span></span></div>

<header class="site-header" id="site-header" data-header>
	<div class="container site-header__inner">

		<div class="site-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); // outputs its own home link — must NOT be nested in another <a> ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-link" aria-label="<?php bloginfo( 'name' ); ?>">
					<img src="<?php echo esc_url( AEON_URI . '/assets/images/logo-wordmark.jpeg' ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="160" height="50" class="brand-logo-img">
				</a>
			<?php endif; ?>
		</div>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'aeon' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'site-nav__menu',
				'fallback_cb'    => 'aeon_default_menu',
				'depth'          => 2,
			) );
			?>
		</nav>

		<div class="site-header__actions">
			<?php
			/**
			 * Language switcher is temporarily hidden. Flip AEON_SHOW_LANG_SWITCH to
			 * true (or define the constant) to bring the EN/AR button back — markup
			 * and behaviour are kept fully intact.
			 */
			$aeon_show_lang = defined( 'AEON_SHOW_LANG_SWITCH' ) ? AEON_SHOW_LANG_SWITCH : false;
			?>
			<?php if ( $aeon_show_lang ) : ?>
				<a class="lang-switch" href="<?php echo aeon_switch_url(); ?>" aria-label="Switch language">
					<?php aeon_e( 'lang_switch' ); ?>
				</a>
			<?php endif; ?>
			<a class="btn btn--primary btn--sm header-cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				<span><?php aeon_e( 'cta_request' ); ?></span>
			</a>
			<button class="nav-toggle" aria-label="<?php esc_attr_e( 'Menu', 'aeon' ); ?>" aria-expanded="false" data-nav-toggle>
				<span></span><span></span><span></span>
			</button>
		</div>

	</div>
</header>

<div class="mobile-menu" data-mobile-menu aria-hidden="true">
	<div class="mobile-menu__inner">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'mobile-menu__list',
			'fallback_cb'    => 'aeon_default_menu',
			'depth'          => 1,
		) );
		?>
		<a class="btn btn--primary btn--block" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php aeon_e( 'cta_request' ); ?></a>
		<?php if ( $aeon_show_lang ) : // Hidden alongside the header switcher — see AEON_SHOW_LANG_SWITCH above. ?>
			<a class="mobile-menu__lang" href="<?php echo aeon_switch_url(); ?>"><?php echo aeon_is_rtl() ? 'English' : 'العربية'; ?></a>
		<?php endif; ?>
	</div>
</div>

<main id="main" class="site-main">
<?php
/**
 * Fallback nav so the theme works before menus are assigned.
 */
function aeon_default_menu( $args = array() ) {
	$items = array(
		'/'         => aeon_t( 'nav_home' ),
		'/about/'   => aeon_t( 'nav_about' ),
		'/services/'=> aeon_t( 'nav_services' ),
		'/work/'    => aeon_t( 'nav_work' ),
		'/blog/'    => aeon_t( 'nav_blog' ),
		'/contact/' => aeon_t( 'nav_contact' ),
	);
	$class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'site-nav__menu';
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as $path => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( $path ) ), esc_html( $label ) );
	}
	echo '</ul>';
}
