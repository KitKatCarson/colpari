<?php
/**
 * Project: Minerva KB
 * Copyright: 2015-2016 @KonstruktStudio
 */

require_once(MINERVA_KB_PLUGIN_DIR . 'lib/widgets/recent-topics.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/widgets/recent-articles.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/widgets/table-of-contents.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/widgets/search.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/widgets/breadcrumbs.php');
require_once(MINERVA_KB_PLUGIN_DIR . 'lib/widgets/content-tree.php');

/**
 * Sidebars and widgets
 */
class MinervaKB_Widgets {

	/**
	 * Constructor
	 */
	public function __construct () {
		add_action( 'widgets_init', array($this, 'register_sidebars') );
		add_action( 'widgets_init', array($this, 'register_widgets') );
	}

	/**
	 * Register all plugin sidebars
	 */
	public function register_sidebars() {
		register_sidebar(
			apply_filters('minerva_home_sidebar_options',
				array(
					'name' => __('KB Home Sidebar', 'minerva-kb'),
					'id' => 'sidebar-kb-page',
					'description' => __('Add widgets here to appear in your Knowledge Base sidebar.', 'minerva-kb'),
					'before_widget' => '<section id="%1$s" class="widget mkb-widget %2$s">',
					'after_widget' => '</section>',
					'before_title' => '<h2 class="mkb-widget-title">',
					'after_title' => '</h2>',
				)
			)
		);

		register_sidebar(
			apply_filters('minerva_topic_sidebar_options',
				array(
					'name' => __('KB Topic Sidebar', 'minerva-kb'),
					'id' => 'sidebar-kb-topic',
					'description' => __('Add widgets here to appear in your Knowledge Base sidebar.', 'minerva-kb'),
					'before_widget' => '<section id="%1$s" class="widget mkb-widget %2$s">',
					'after_widget' => '</section>',
					'before_title' => '<h2 class="mkb-widget-title">',
					'after_title' => '</h2>',
				)
			)
		);

		if (!MKB_Options::option( 'tags_disable' )) {

			register_sidebar(
				apply_filters('minerva_tag_sidebar_options',
					array(
						'name' => __('KB Tag Sidebar', 'minerva-kb'),
						'id' => 'sidebar-kb-tag',
						'description' => __('Add widgets here to appear in your Knowledge Base sidebar.', 'minerva-kb'),
						'before_widget' => '<section id="%1$s" class="widget mkb-widget %2$s">',
						'after_widget' => '</section>',
						'before_title' => '<h2 class="mkb-widget-title">',
						'after_title' => '</h2>',
					)
				)
			);

		}

		register_sidebar(
			apply_filters('minerva_article_sidebar_options',
				array(
					'name' => __('KB Article Sidebar', 'minerva-kb'),
					'id' => 'sidebar-kb-article',
					'description' => __('Add widgets here to appear in your Knowledge Base sidebar.', 'minerva-kb'),
					'before_widget' => '<section id="%1$s" class="widget mkb-widget %2$s">',
					'after_widget' => '</section>',
					'before_title' => '<h2 class="mkb-widget-title">',
					'after_title' => '</h2>',
				)
			)
		);

		register_sidebar(
			apply_filters('minerva_search_sidebar_options',
				array(
					'name' => __('KB Search Sidebar', 'minerva-kb'),
					'id' => 'sidebar-kb-search',
					'description' => __('Add widgets here to appear in your Knowledge Base Search results page sidebar.', 'minerva-kb'),
					'before_widget' => '<section id="%1$s" class="widget mkb-widget %2$s">',
					'after_widget' => '</section>',
					'before_title' => '<h2 class="mkb-widget-title">',
					'after_title' => '</h2>',
				)
			)
		);
	}

	/**
	 * Registers plugin widgets
	 */
	public function register_widgets() {
		register_widget( 'MKB_Recent_Topics_Widget' );
		register_widget( 'MKB_Recent_Articles_Widget' );
		register_widget( 'MKB_Table_Of_Contents_Widget' );
		register_widget( 'MKB_Search_Widget' );
		register_widget( 'MKB_Breadcrumbs_Widget' );
		register_widget( 'MKB_Content_Tree_Widget' );
	}
}
