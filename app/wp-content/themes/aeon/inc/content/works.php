<?php
/**
 * Our Works section content type: the "أعمالنا" portfolio cards.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Portfolio CPT + Work Category taxonomy.
 */
function aeon_register_portfolio_cpt() {
	register_post_type( 'portfolio', array(
		'labels'       => array(
			'name'               => 'أعمالنا',
			'singular_name'      => 'مشروع',
			'menu_name'          => 'أعمالنا',
			'add_new'            => 'إضافة مشروع',
			'add_new_item'       => 'إضافة مشروع جديد',
			'edit_item'          => 'تعديل المشروع',
			'new_item'           => 'مشروع جديد',
			'view_item'          => 'عرض المشروع',
			'search_items'       => 'بحث في الأعمال',
			'not_found'          => 'لا توجد مشاريع',
			'not_found_in_trash' => 'لا توجد مشاريع في المهملات',
			'all_items'          => 'كل الأعمال',
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_menu' => false, // surfaced under "محتوى الموقع" by inc/content/menu.php.
		'menu_icon'    => 'dashicons-portfolio',
		'rewrite'      => array( 'slug' => 'work' ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'show_in_rest' => true,
	) );

	register_taxonomy( 'work_category', 'portfolio', array(
		'labels'       => array(
			'name'          => 'تصنيفات الأعمال',
			'singular_name' => 'تصنيف',
			'add_new_item'  => 'إضافة تصنيف',
			'edit_item'     => 'تعديل التصنيف',
			'all_items'     => 'كل التصنيفات',
			'search_items'  => 'بحث في التصنيفات',
		),
		'public'       => true,
		'hierarchical' => true,
		'show_in_menu' => false, // managed inline from the project editor.
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'work-category' ),
	) );
}
add_action( 'init', 'aeon_register_portfolio_cpt' );

/** Arabic placeholder for the project title field. */
function aeon_portfolio_title_placeholder( $text, $post ) {
	return ( $post && 'portfolio' === $post->post_type ) ? 'اسم المشروع' : $text;
}
add_filter( 'enter_title_here', 'aeon_portfolio_title_placeholder', 10, 2 );

/** Arabic label for the featured-image box on the project editor. */
function aeon_portfolio_thumbnail_label( $content, $post_id ) {
	if ( 'portfolio' === get_post_type( $post_id ) ) {
		$content = str_replace( __( 'Set featured image' ), 'تعيين صورة المشروع', $content );
		$content = str_replace( __( 'Remove featured image' ), 'إزالة الصورة', $content );
	}
	return $content;
}
add_filter( 'admin_post_thumbnail_html', 'aeon_portfolio_thumbnail_label', 10, 2 );

/**
 * Meta box: "wide card" display toggle.
 */
function aeon_portfolio_meta_box() {
	add_meta_box( 'aeon_work_meta', 'خيارات العرض', 'aeon_portfolio_meta_cb', 'portfolio', 'side' );
}
add_action( 'add_meta_boxes', 'aeon_portfolio_meta_box' );

function aeon_portfolio_meta_cb( $post ) {
	wp_nonce_field( 'aeon_work_meta', 'aeon_work_nonce' );
	$wide     = get_post_meta( $post->ID, '_aeon_wide', true );
	$industry = (string) get_post_meta( $post->ID, '_aeon_industry', true );
	?>
	<p style="text-align:right">
		<label>
			<input type="checkbox" name="aeon_wide" value="1" <?php checked( $wide, '1' ); ?>>
			بطاقة عريضة (تمتد على عمودين)
		</label>
	</p>
	<p style="text-align:right">
		<label for="aeon_industry" style="display:block;font-weight:600;margin-bottom:4px">القطاع</label>
		<select name="aeon_industry" id="aeon_industry" style="width:100%">
			<option value="">— بدون —</option>
			<?php foreach ( aeon_section_terms( 'aeon_industry' ) as $term ) : ?>
				<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $industry, $term->slug ); ?>>
					<?php echo esc_html( $term->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<span class="description">يظهر كوسم أعلى مجموعة الأعمال في صفحة أعمالنا.</span>
	</p>
	<?php
}

/**
 * Save portfolio meta.
 */
function aeon_save_portfolio_meta( $post_id ) {
	if ( ! aeon_can_save_meta( $post_id, 'aeon_work_nonce', 'aeon_work_meta' ) ) {
		return;
	}
	update_post_meta( $post_id, '_aeon_wide', isset( $_POST['aeon_wide'] ) ? '1' : '' );
	update_post_meta(
		$post_id,
		'_aeon_industry',
		isset( $_POST['aeon_industry'] ) ? sanitize_key( wp_unslash( $_POST['aeon_industry'] ) ) : ''
	);
}
add_action( 'save_post_portfolio', 'aeon_save_portfolio_meta' );
