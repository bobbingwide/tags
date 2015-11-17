<?php // (C) Copyright Bobbing Wide 2015

/**
 * batch migrate TAGS from Drupal to WordPress
 *
 * - Migrate Categories and Tag definitions from vocabulary	- see the main routine ( tags.php )
 * - Migrate post_types from node_type - see the main routine ( tags.php )
 * - Migrate taxonomies for post_types from vocabulary_node_types	- see the main routine ( tags.php )
 * - Migrate Category and Tags terms from term_node, term_data and term_hierarchy
 * - Define fields for any post type from content_node_field
 * - Register fields for object type from content_node_field_instance
 * - Migrate different bits of content in the correct order.
 * 
 */
function td2w_lazy_run() {
	//  gobang();
	static $count = 0;
	$count++;
	echo "Running td2w_lazy_run: $count" . PHP_EOL ;
	
	oik_require( "includes/class-td2w-terms.php", "tags" );
	$terms = new TD2W_terms();
	//$terms->migrate();
	
	//oik_require( "includes/class-td2w-files.php", "tags" );
	//$files = new TD2W_files();
	
	//oik_require( "includes/class-td2w-courses.php", "tags" );
  //$courses = new TD2W_courses();
	
	//oik_require( "includes/class-td2w-trophies.php", "tags" );
  // $trophies = new TD2W_trophies();
	
	oik_require( "includes/class-td2w-users.php", "tags" );
	$users = new TD2W_users();
	
	oik_require( "includes/class-td2w-players.php", "tags" );
	$players = new TD2W_players( $users );
	// tags_register_player();
	// tags_register_event();
	// tags_register_result();
	
	

}



/**
 *

y/n | table_name                  | table_rows | target
-| ----------------------------- | ------------ | ------ 
n | access                      |          0 |
n | actions                     |         24 |
n | actions_aid                 |          2 |
n | advanced_help_index         |        171 |
n | aggregator_category         |          1 |
n | aggregator_category_feed    |          1 |
n | aggregator_category_item    |          0 |
n | aggregator_feed             |          1 |
n | aggregator_item             |          0 |
n | authmap                     |          0 |
n | backup_migrate_destinations |          0 |
n | backup_migrate_profiles     |          1 |
n | backup_migrate_schedules    |          0 |
n | batch                       |          0 | 
? | blocks                      |         86 | widgets
n | blocks_roles                |          0 | widgets
n | boxes                       |          3 |
n | cache                       |         21 |
n | cache_admin_menu            |          1 |
n | cache_block                 |          0 |
n | cache_content               |         11 |
n | cache_filter                |          8 |
n | cache_form                  |          0 |
n | cache_location              |          3 |
n | cache_menu                  |         74 |
n | cache_page                  |          0 |
n | cache_rules                 |          3 |
n | cache_tax_image             |          0 |
n | cache_update                |          1 |
n | cache_views                 |         19 |
n | cache_views_data            |          0 |
n | captcha_points              |          8 |
n | captcha_sessions            |          0 |
? | comments                    |          3 | comments
? | contact                     |          6 | widgets / contact form
y | content_field_competition   |        406 | noderef 
| content_field_image         |         50 |
| content_field_ntps          |        187 |
| content_field_photo         |         82 |
| content_field_player        |       1175 |
| content_field_runnerup      |        684 |
| content_field_third         |        684 |
| content_field_winner        |        684 |
| content_group               |          2 |
| content_group_fields        |         10 |
| content_node_field          |         27 |
| content_node_field_instance |         33 |
| content_type_competitor     |          0 |
| content_type_competitors    |         35 |
y | content_type_course         |         31 | field_website_url, field_website_title
| content_type_event          |        171 |
| content_type_player         |         81 |
| content_type_profile        |          0 |
| content_type_result         |        352 |
| content_type_trophy         |         19 |
| ctools_css_cache            |          0 |
| ctools_object_cache         |          0 |
| date_format_locale          |          0 |
| date_format_types           |          5 |
| date_formats                |         37 |
| devel_queries               |          0 |
| devel_times                 |          0 |
| fb_app                      |          1 |
| fckeditor_role              |          3 |
| fckeditor_settings          |          3 |
| files                       |        108 |
| filter_formats              |          2 |
| filters                     |          8 |
| flood                       |          0 |
| forum                       |          0 |
| gmap_taxonomy_node          |          0 |
| gmap_taxonomy_term          |          0 |
| history                     |          4 |
| imagecache_action           |          2 |
| imagecache_preset           |          2 |
| imagemenu                   |          0 |
y | location                    |         31 |
y | location_instance           |         31 |
| location_phone              |          7 |
| location_search_work        |          0 |
| menu_custom                 |          5 |
| menu_links                  |       1081 |
| menu_router                 |       1191 |
y | node                        |        744 |
| node_access                 |          1 |
| node_comment_statistics     |        744 |
| node_counter                |          0 |
? | node_revisions              |        744 | same as node?
| node_type                   |         15 |
| nodewords                   |       3983 |
| nodewords_custom            |          0 |
| oauth_consumer              |          1 |
| oauth_nonce                 |          0 |
| oauth_token                 |          1 |
| openid_association          |          0 |
| openid_nonce                |          0 |
| page_manager_handlers       |          1 |
| page_manager_pages          |          0 |
| page_manager_weights        |          0 |
| page_title                  |          0 |
| panels_display              |          1 |
| panels_layout               |          1 |
| panels_mini                 |          0 |
| panels_node                 |          0 |
| panels_pane                 |         12 |
| panels_renderer_pipeline    |          0 |
| path_redirect               |          0 |
| permission                  |          3 |
| poll                        |          0 |
| poll_choices                |          0 |
| poll_votes                  |          0 |
| profile_fields              |          0 |
| profile_values              |          0 |
| role                        |          3 |
| rules_rules                 |          2 |
| rules_sets                  |          2 |
| search_dataset              |        953 |
| search_index                |      47056 |
| search_node_links           |          2 |
| search_total                |       7138 |
| semaphore                   |          0 |
| seo_checklist               |         51 |
| seo_group                   |         12 |
| sessions                    |         20 |
n | simplenews_mail_spool       |          0 |
| simplenews_newsletters      |        208 |
? | simplenews_snid_tid         |         23 |
? | simplenews_subscriptions    |         23 |
? | site_verify                 |          3 |
n | stylizer                    |          0 |
n | system                      |        248 |
n | taxonomy_menu               |          0 | 
y | term_data                   |         21 | tid vid name desc weight
y | term_hierarchy              |         21 | tid parent
n | term_image                  |          0 |
y | term_node                   |        556 | nid vid tid
n | term_relation               |          0 |
n | term_synonym                |          0 |
| trigger_assignments         |          0 |
| twitter                     |          0 |
| twitter_account             |          0 |
| upload                      |         21 |
| url_alias                   |        817 |
| users                       |         42 |
| users_roles                 |          6 |
? | variable                    |        654 | options
| views_display               |         29 |
| views_object_cache          |          4 |
| views_view                  |          9 |
y | vocabulary                  |          4 | terms
y | vocabulary_node_types       |          8 |
| watchdog                    |       1007 |
| xmlsitemap                  |        806 |
| xmlsitemap_node             |        744 |
| xmlsitemap_taxonomy         |         21 |
| xmlsitemap_user             |         41 |
| xmlsitemap_user_role        |          0 |
| zipcodes                    |          0 |
 | ----------------------------- | ------------ | 
158 rows in set (1.10 sec)

*/

