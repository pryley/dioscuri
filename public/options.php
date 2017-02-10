<?php defined( 'ABSPATH' ) or die();

define( 'POLLUX_DISABLE_POSTS', true );
define( 'POLLUX_ASSET_REWRITE', true );

/**
 * Post Type Options
 *
 * @return array
 */
function _post_type_options()
{
	$page_columns = ['title', 'slug', 'author', 'comments', 'date'];
	$post_columns = ['title', 'slug', 'author', 'categories', 'comments', 'date'];

	$comments = get_option( 'disable_comments_options' );

	foreach( ['post', 'page'] as $pt ) {
		if( isset( $comments['disabled_post_types'] ) && in_array( $pt, $comments['disabled_post_types'] ) ) {
			${"{$pt}_columns"} = array_diff( ${"{$pt}_columns"}, ['comments'] );
		}
	}

	return [
		[
			'post_type'     => 'page',
			'columns'       => $page_columns,
		],
		[
			'post_type'     => 'post',
			'columns'       => $post_columns,
		],
		// [
		// 	'post_type'     => 'gallery',
		// 	'slug'          => 'galleries',
		// 	'single'        => __( 'Gallery', 'pollux' ),
		// 	'plural'        => __( 'Galleries', 'pollux' ),
		// 	'menu_icon'     => 'dashicons-images-alt2',
		// 	'menu_position' => 20,
		// 	'supports'      => ['title'],
		// 	'columns'       => ['title', 'slug', 'media', 'date'],
		// 	// 'public'        => false,
		// 	'has_archive'   => true,
		// ],
		// [
		// 	'post_type'     => 'product',
		// 	'slug'          => 'products',
		// 	'single'        => __( 'Product', 'pollux' ),
		// 	'plural'        => __( 'Products', 'pollux' ),
		// 	'menu_icon'     => 'dashicons-cart',
		// 	'menu_position' => 20,
		// 	'supports'      => ['title', 'editor'],
		// 	'columns'       => ['title', 'slug', 'media', 'date'],
		// 	'has_archive'   => true,
		// ],
	];
}

/**
 * Post Type Column Options
 *
 * @return array
 */
function _post_type_column_options()
{
	$comments = sprintf(
		'<span class="vers comment-grey-bubble" title="%1$s"><span class="screen-reader-text">%1$s</span></span>',
		__( 'Comments', 'pollux' )
	);

	return [
		'author'     => __( 'Author', 'pollux' ),
		'categories' => __( 'Categories', 'pollux' ),
		'comments'   => $comments,
		'date'       => __( 'Date', 'pollux' ),
		'media'      => __( 'Media', 'pollux' ),
		'slug'       => __( 'Slug', 'pollux' ),
		'thumbnail'  => __( 'Slide Image', 'pollux' ),
		'title'      => __( 'Title', 'pollux' ),
	];
}

/**
 * Archive Page Options
 *
 * If an array has an id of 'static', the metabox will have no frame or title.
 *
 * @return array
 */
function _post_type_settings_options()
{
	return [
		[
			'id'         => 'static',
			'title'      => 'Page Content',
			'context'    => 'static',
			'group'      => true,
			'fields'     => [
				[
					'id'    => 'title',
					'name'  => '',
					'type'  => 'text',
					'placeholder' => 'Enter title here (optional)',
				],
				[
					'id'    => 'content',
					'name'  => '',
					'type'  => 'wysiwyg',
				],
			],
		],
		[
			'title'  => __( 'Summary', 'pollux' ),
			'group'  => true,
			'fields' => [
				[
					'id'   => 'summary',
					'type' => 'textarea',
				],
			],
		],
		[
			'title'      => 'Featured Image',
			'context'    => 'side',
			'priority'   => 'low',
			'group'      => true,
			'fields'     => [
				[
					'id'    => 'featured',
					'name'  => '',
					'type'  => 'image_advanced',
					'max_file_uploads' => 1,
				],
			],
		],
	];
}

/**
 * Meta Box Options
 *
 * @return array
 */
function _meta_box_options()
{
	return [
		[
			'post_types' => ['page'],
			'title'      => __( 'Page Title', 'pollux' ),
			'condition'  => [
				'not_page' => get_option( 'page_on_front' ),
			],
			'fields'     => [
				[
					'id'   => 'summary',
					'name' => __( 'Summary', 'pollux' ),
					'type' => 'textarea',
				],
				[
					'id'   => 'hide_title',
					'name' => __( 'Hide the page title?', 'pollux' ),
					'type' => 'checkbox',
				],
			],
		],
		[
			'post_types' => ['page'],
			'title'      => __( 'Callouts', 'pollux' ),
			'condition'  => [
				'page' => get_option( 'page_on_front' ),
			],
			'priority'   => 'low',
			'fields'     => [
				[
					'id'   => 'callouts',
					'type' => 'text',
					'clone' => true,
					'sort_clone' => true,
					'clone_max' => 3,
					'class' => 'full-width',
				],
			],
		],
		[
			'post_types' => ['gallery'],
			'title'      => __( 'Gallery Media', 'pollux' ),
			'fields'     => [
				[
					'id'   => 'media',
					'type' => 'image_advanced',
					'size' => 'thumbnail',
					'max_file_uploads' => 30,
				],
			],
		],
		[
			'post_types' => ['gallery'],
			'title'      => __( 'Gallery Options', 'pollux' ),
			'context'    => 'side',
			'priority'   => 'core',
			'fields'     => [
				[
					'id'   => 'exclude',
					'name' => __( 'Exclude from Galleries page', 'pollux' ),
					'type' => 'checkbox_alt',
				],
			],
		],
		[
			'post_types' => ['download'],
			'title'      => __( 'Product Details', 'pollux' ),
			'fields'     => [
				[
					'id'   => 'background',
					'name' => __( 'Background Color', 'pollux' ),
					'type' => 'color',
				],

				[
					'id'   => 'summary',
					'name' => __( 'Summary', 'pollux' ),
					'type' => 'textarea',
				],
				[
					'id'   => 'screenshots',
					'name' => __( 'Screenshots', 'pollux' ),
					'type' => 'image_advanced',
					'size' => 'thumbnail',
					'max_file_uploads' => 3,
				],
				[
					'id' => 'features',
					'name' => __( 'Features', 'pollux' ),
					'type' => 'text',
					'class' => 'full-width',
					'clone' => true,
					'sort_clone' => true,
				],
				[
					'id' => 'requirements',
					'name' => __( 'Requirements', 'pollux' ),
					'type' => 'text',
					'class' => 'full-width',
					'clone' => true,
					'sort_clone' => true,
				],
			],
		],
	];
}

/**
 * Site Settings Options
 *
 * @return array
 */
function _site_settings_options()
{
	return [
		[
			'id'       => 'global',
			'title'    => __( 'Global Settings', 'pollux' ),
			'fields'   => [
				[
					'id'    => 'copyright',
					'name'  => __( 'Copyright blurb', 'pollux' ),
					'type'  => 'text',
					'class' => 'full-width',
					'polylang' => true,
				],
				[
					'id'    => 'seo_enabled',
					'name'  => __( 'Enable built-in SEO?', 'pollux' ),
					'type'  => 'checkbox',
				],
				[
					'id'    => 'seo_title',
					'name'  => __( 'SEO Title (prefix)', 'pollux' ),
					'type'  => 'text',
					'class' => 'full-width',
					'polylang' => true,
				],
				[
					'id'    => 'seo_description',
					'name'  => __( 'SEO Description', 'pollux' ),
					'type'  => 'textarea',
					'class' => 'full-width',
					'polylang' => true,
				],
				[
					'id'    => 'seo_keywords',
					'name'  => __( 'SEO Keywords', 'pollux' ),
					'type'  => 'text',
					'class' => 'full-width',
					'polylang' => true,
				],
				[
					'id'      => 'robots',
					'name'    => 'Robots <span class="help"><a href="http://www.robotstxt.org/meta.html" target="_blank">[?]</a></span>',
					'desc'    => __( 'Make sure you understand what this does before you change it.', 'pollux' ),
					'type'    => 'select',
					'default' => 'index, follow',
					'options' => [
						'index, follow',
						'noindex, follow',
						'index, nofollow',
						'noindex, nofollow',
					],
				],
			],
		],
		[
			'id'       => 'pages',
			'title'    => __( 'Pages', 'pollux' ),
			'fields'   => [
				[
					'id'          => 'account',
					'name'        => __( 'Account Page', 'pollux' ),
					'type'        => 'post',
					'post_type'   => ['page'],
					'placeholder' => __( 'Select a page', 'pollux' ),
					'query_args'  => [
						'lang' => 'en',
					],
				],
				[
					'id'          => 'contact',
					'name'        => __( 'Contact Page', 'pollux' ),
					'type'        => 'post',
					'post_type'   => ['page'],
					'placeholder' => __( 'Select a page', 'pollux' ),
					'query_args'  => [
						'lang' => 'en',
					],
				],
				[
					'id'          => 'galleries',
					'name'        => __( 'Galleries Page', 'pollux' ),
					'type'        => 'post',
					'post_type'   => ['page'],
					'placeholder' => __( 'Select a page', 'pollux' ),
					'query_args'  => [
						'lang' => 'en',
					],
				],
			],
		],
		[
			'id'       => 'gallery',
			'title'    => __( 'Gallery Settings', 'pollux' ),
			'fields'   => [
				[
					'id'   => 'crossfade_delay',
					'name' => __( 'Crossfade Delay', 'pollux' ),
					'type' => 'range',
					'min'  => 2,
					'max'  => 10,
					'step' => 1,
				],
				[
					'id'   => 'keyboard',
					'name' => __( 'Keyboard navigation', 'pollux' ),
					'type' => 'checkbox',
				],
				[
					'id'   => 'prevnext',
					'name' => __( 'Previous/Next buttons', 'pollux' ),
					'type' => 'checkbox',
				],
			],
		],
		[
			'id'       => 'contact',
			'title'    => __( 'Contact Details', 'pollux' ),
			'fields'   => [
				[
					'id'    => 'email',
					'name'  => __( 'Email', 'pollux' ),
					'type'  => 'text',
					'class' => 'full-width',
				],
				[
					'id'    => 'twitter',
					'name'  => __( 'Twitter Profile Url', 'pollux' ),
					'type'  => 'text',
					'class' => 'full-width',
				],
			],
		],
		[
			'id'       => 'services',
			'title'    => __( '3rd Party Services', 'pollux' ),
			'fields'   => [
				[
					'id'    => 'google',
					'name'  => 'Google Tracking ID <span class="help"><a href="http://www.google.com/analytics/" target="_blank">[?]</a></span>',
					'type'  => 'text',
				],
				[
					'id'    => 'clicky',
					'name'  => 'Clicky Site ID <span class="help"><a href="http://clicky.com/" target="_blank">[?]</a></span>',
					'type'  => 'text',
				],
			],
		],
	];
}
