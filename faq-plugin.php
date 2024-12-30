<?php
/**
 * Plugin Name:       Faq-Plugin
 * Description:       A simple FAQ plugin to insert your faqs in any posts.
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Version:           0.1.0
 * Author:            Sandip Mishra
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       faq
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'src/render.php';

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 */
function create_block_faq_block_init() {
    register_block_type( __DIR__ . '/build', [
        'render_callback' => 'faq_render_callback', // Point to your render function
    ]);
}
add_action( 'init', 'create_block_faq_block_init' );

function datm_reg_custom_post_type_faq(){
    register_post_type( 'faq', [
        'public'                        => true,
        'publicly_queryable'            => true,
        'show_in_rest'                  => true,
        'show_in_nav_menus'             => true,
        'show_in_admin_bar'             => true,
        'exclude_from_search'           => false,
        'show_ui'                       => true,
        'show_in_menu'                  => true,
        'menu_icon'                     => 'dashicons-paperclip',
        'hierarchical'                  => false,
        'has_archive'                   => true,
        'query_var'                     => 'faq',
        'map_meta_cap'                  => true,
        'rewrite'   => [
            'slug'          => 'faqs',
            'with_front'    => 'false',
            'pages'         => true,
            'feeds'         => true,
            'ep_mask'       => EP_PERMALINK,
        ],
        'supports'  => [
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'custom-fields'
        ],
        'labels'    => [
            'name'                      => 'FAQs',
            'singular_name'             => 'FAQ',
            'add_new'                   => 'Add New',
            'add_new_item'              => 'Add New FAQ',
            'edit_item'                 => 'Edit FAQ',
            'new_item'                  => 'New FAQ',
            'view_item'                 => 'View FAQ',
            'view_items'                => 'View FAQs',
            'search_items'              => 'Search FAQs',
            'not_found'                 => 'No FAQs Found',
            'not_found_in_trash'        => 'No FAQs found in Trash',
            'all_items'                 => 'All FAQs',
            'archives'                  => 'FAQ Archives',
            'attributes'                => 'FAQ Attributes',
            'insert_into_item'          => 'Insert into FAQ',
            'uploaded_to_this_item'     => 'Uploaded to this FAQ',
            'featured_image'            => 'FAQ image',
            'set_featured_image'        => 'Set FAQ image',
            'remove_featured_image'     => 'Remove FAQ image',
            'use_featured_image'        => 'Use as FAQ image',
            'filter_items_list'         => 'Filter FAQs list',
            'items_list_navigation'     => 'FAQ list navigation',
            'items_list'                => 'FAQs list',
            'item_published'            => 'FAQ Published',
            'item_published_privately'  => 'FAQ published Privately',
            'item_reverted_to_draft'    => 'FAQ Reverted to draft',
            'item_scheduled'            => 'FAQ scheduled',
            'item_updated'              => 'FAQ Updated.'
        ],
        'map_meta_cap'      => true,
        'taxonomies'        => [
            'category',
        ],
    ]);
}
add_action( 'init', 'datm_reg_custom_post_type_faq' );

add_filter('use_block_editor_for_post_type', 'datm_disable_gutenberg', 10, 2);
function datm_disable_gutenberg($current_status, $post_type)
{
    if ($post_type === 'faq') return false;
    return $current_status;
}

function faq_plugin_enqueue_styles() {
    // Enqueue front-end style (style-index.css)
    wp_enqueue_style(
        'faq-plugin-style', // Handle
        plugin_dir_url(__FILE__) . 'build/style-index.css', // Path to the CSS file
        [], // Dependencies (empty array means no dependencies)
        filemtime(plugin_dir_path(__FILE__) . 'build/style-index.css'), // Version based on file modification time
        'all' // Media type
    );

    // Enqueue editor style (index.css)
    wp_enqueue_style(
        'faq-plugin-editor-style', // Handle for editor style
        plugin_dir_url(__FILE__) . 'build/index.css', // Path to the editor CSS file
        [], // Dependencies
        filemtime(plugin_dir_path(__FILE__) . 'build/index.css'), // Version based on file modification time
        'all' // Media type
    );
}
add_action('wp_enqueue_scripts', 'faq_plugin_enqueue_styles'); // Front-end styles
add_action('enqueue_block_editor_assets', 'faq_plugin_enqueue_styles'); // Editor styles

// Register the dynamic block
function faq_plugin_register_block() {
    register_block_type_from_metadata(__DIR__, [
        'render_callback' => 'faq_render_callback',
    ]);
}
add_action('init', 'faq_plugin_register_block');
