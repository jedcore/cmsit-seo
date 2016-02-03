<?php
/**
 * Functions used on theme installation
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Our theme has advanced installation process with quick setup wizard.
 * We try to do all hard work automatically:
 * - Install bundled plugins
 * - Configure basic settings
 * 	> create system templates
 * 	> create basic menu and activate MegaMainMenu for it
 * 	> regenerate custom css
 * 	> setup LiveComposer tutorial pages
 * 	> setup default settings for bundled plugins
 * - Import demo content
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    http://themeforest.net/licenses
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* ----------------------------------------------------------------------
* Perform custom fucntions on theme activation
* http://wordpress.stackexchange.com/a/80320/34582
*/

/**
* ----------------------------------------------------------------------
* Theme has been just activated
*/
// update_option( LBMN_THEME_NAME . '_required_plugins_installed', false);

if ( is_admin() && $pagenow == "themes.php" ) {


	/*
	if( $_GET['test'] == '1' ) {

		$import_path_demo_content = $theme_dir . '/design/demo-content/posts/';

		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator($import_path_demo_content) ) as $filename) {
			if ( stristr($filename, '.xml') ) {

				echo "<br/> $filename";

			}
		}
	}
	*/


	// if ( isset($_GET['test']) ) {
	// Left here for debugging
	// }

	// Update theme option '_required_plugins_installed'
	// if URL has ?plugins=installed variable set
	if ( isset($_GET['plugins']) && $_GET['plugins'] == 'installed' ) {
		update_option( LBMN_THEME_NAME . '_required_plugins_installed', true);
	}

	// Update theme option '_basic_config_done'
	// if URL has ?basic_setup=completed variable set
	if ( isset($_GET['basic_setup']) && $_GET['basic_setup'] == 'completed' ) {
		update_option( LBMN_THEME_NAME . '_basic_config_done', true);
		define ('LBMN_THEME_CONFUGRATED', true);
	}

	// Update theme option '_basic_config_done'
	// if URL has ?demoimport=completed variable set
	if ( isset($_GET['demoimport']) && $_GET['demoimport'] == 'completed' ) {
		update_option( LBMN_THEME_NAME . '_democontent_imported', true);
	}


	if ( isset($_GET['show_quicksetup']) ) {
		add_action( 'admin_notices', 'lbmn_setmessage_themeinstall' );
	}


	$current_page_tgmpa = false;

	if ( isset($_GET['page']) && stristr($_GET['page'], 'install-required-plugins') ) {
		$current_page_tgmpa = true;
	}

	if ( !get_option(LBMN_THEME_NAME . '_hide_quicksetup') && !$current_page_tgmpa ) {
		add_action( 'admin_notices', 'lbmn_setmessage_themeinstall' );
	}

	// Reset quick theme installer steps
	if ( isset($_GET['reset_quicksetup']) ) {
		update_option( LBMN_THEME_NAME . '_required_plugins_installed', false);
		update_option( LBMN_THEME_NAME . '_basic_config_done', false);
		update_option( LBMN_THEME_NAME . '_democontent_imported', false);
	}

	// Import Demo Ninja Forms manually by visiting /wp-admin/themes.php?import-forms
	if ( isset($_GET['import-forms']) ) {
		lbmn_ninjaforms_import();
	}
}

/**
 * ----------------------------------------------------------------------
 * Check if required plugins were manually installed
 */


function lbmn_required_plugins_install_check() {

	if ( TGM_Plugin_Activation::get_instance()->is_tgmpa_complete() ) {
		// Update theme option '_required_plugins_installed'
		update_option( LBMN_THEME_NAME . '_required_plugins_installed', true);

		// Mark first step 'Install Plugins' as done
		echo '<script type="text/javascript">jQuery(\'.step-plugins\').addClass(\'step-completed\');</script>';
	} else {
		update_option( LBMN_THEME_NAME . '_required_plugins_installed', false);
	}


	// if ( ! get_option( LBMN_THEME_NAME . '_required_plugins_installed' ) ) {
	// Proceed only if '_required_plugins_installed' not already market as true


		/*
		global $tgmpa_settings_errors;


		$current_tgmpa_message = '';

		if (is_array($tgmpa_settings_errors)) {
			foreach ($tgmpa_settings_errors as $message) {
				$current_tgmpa_message .= $message['message'];
			}
		}

		$current_wpadmin_screen = get_current_screen();
		lbmn_debug_console( $current_wpadmin_screen->id );

		// Don't check on TGMPA installation page as there is no notices
		// if ($current_wpadmin_screen->id != 'appearance_page_install-required-plugins' ) {
		if ($current_wpadmin_screen->id == 'themes' ) {

			// echo "MESSAGE: $current_tgmpa_message";

			// If message has no link to install-required-plugins page then all
			// required plugins has been installed
			if ( ! stristr($current_tgmpa_message, 'install-required-plugins') ) {

				// Update theme option '_required_plugins_installed'
				update_option( LBMN_THEME_NAME . '_required_plugins_installed', true);

				// Mark first step 'Install Plugins' as done
				echo '<script type="text/javascript">jQuery(\'.step-plugins\').addClass(\'step-completed\');</script>';

			} else {
				update_option( LBMN_THEME_NAME . '_required_plugins_installed', false);
			}

			lbmn_debug_console( get_option (LBMN_THEME_NAME . '_required_plugins_installed') );
		}

	// }

	*/
}
add_action( 'admin_footer', 'lbmn_required_plugins_install_check' );
// get_settings_errors() do not return any results earlier than 'admin_footer'


/**
 * ----------------------------------------------------------------------
 * Output Theme Installer HTML
 */

function lbmn_setmessage_themeinstall() {
?>

<img src="<?php echo includes_url() . 'images/spinner.gif' ?>" class="theme-installer-spinner" style="position:fixed; left:50%; top:50%;" />
<style type="text/css">.lumberman-message.quick-setup{display:none;}</style>
<div class="updated lumberman-message quick-setup">
	<div class="message-container">
	<p class="before-header"><?php echo LBMN_THEME_NAME_DISPLAY; ?> Quick Setup</p>
	<h4>Thank you for creating with <a href="<?php echo LBMN_DEVELOPER_URL; ?>" target="_blank"><?php echo LBMN_DEVELOPER_NAME_DISPLAY; ?></a>!</h4>
	<h5>Just a few steps left to release the full power of our theme.</h5>


	<?php
		//Check for GZIP support

		if ( !is_callable( 'gzopen' ) ) {
			echo '<span class="error">Your server doesn\'t support file compression (GZIP). Please <a href="' . LBMN_SUPPORT_URL . '">contact us</a> for alternative installation package.</span>';
		}
	?>

	<!-- Step 1 -->
		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_required_plugins_installed') ) {
				echo '<p id="theme-setup-step-1" class="submit step-plugins">';
			} else {
				echo '<p id="theme-setup-step-1" class="submit step-plugins step-completed">';
			}
		?>
		<span class="step"><span class="number">1</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />

		<span class="step-body"><a href="<?php echo esc_url( add_query_arg( array('page' => 'install-required-plugins'), admin_url('themes.php')) ); ?>#focus-after-installer" class="button button-primary" id="do_plugins-install">Install required plugins</a>
		<?php /*<span class="step-body"><a href="#" class="button button-primary" id="do_plugins-install">Install required plugins</a> */?>
		<span class="step-description">
		Required action to get 100% functionality.<br />
		Installs Page Builder, Mega Menus, Slider, etc.
		</span></span><br />
		<span class="error" style="display:none">Automatic plugin installation failed. Please try to <a href="/wp-admin/themes.php?page=install-required-plugins">install required plugins manually</a>.</span>
		</p>

	<!-- Step 2 -->

		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_basic_config_done') ) {
				echo '<p id="theme-setup-step-2" class="submit step-basic_config">';
			} else {
				echo '<p id="theme-setup-step-2" class="submit step-basic_config step-completed">';
			}
		?>
		<span class="step"><span class="number">2</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />
		<span class="step-body"><a href="#" class="button button-primary" id="do_basic-config" data-ajax-nonce="<?php echo wp_create_nonce( 'wie_import' ); ?>" >Integrate installed plugins</a>
		<span class="step-description">
		Required action to get 100% functionality.<br />
		Configures the plugins to work with our theme.
		</span></span><br />
		<span class="error" style="display:none">Something went wrong (<a href="#" class="show-error-log">show log</a>). Please <a href="<?php echo LBMN_SUPPORT_URL; ?>">contact us</a> for help.</span>
		</p>

	<!-- Step 3 -->

		<?php
			// Recommend user to set permalinks to 'Post name' before installing theme

			if ( get_option('permalink_structure') != '/%postname%/' ) {
				echo '<span class="error">You have <strong>Permalink Settings</strong> set to a default value (not clean URLs). <br />We recommend changing this setting to the "<strong>Post name</strong>" value before continuing. <br /><br />Please, open <a href="/wp-admin/options-permalink.php" target="_blank"><strong>Permalink Settings</strong> page</a> and select the "<strong>Post name</strong>" option before running the 3-rd step.<br /></span><br /><br />';
			}
		?>

		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_democontent_imported') ) {
				echo '<p id="theme-setup-step-3" class="submit step-demoimport">';
			} else {
				echo '<p id="theme-setup-step-3" class="submit step-demoimport step-completed">';
			}
		?>
		<span class="step"><span class="number">3</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />
		<span class="step-body">
		<a href="#" class="button button-primary" id="do_demo-import">Import all demo content</a>
		<span class="step-description">
		Optional step to recreate theme demo website<br />
		on your server.
		</span></span><br />
		<span class="import-progress"> <span class="progress-indicator"></span> </span>
		<!--
		<span style="margin-right:15px;">OR</span>
		<a href="#" class="button button-secondary" id="do_basic-demo-import">Create only 3 basic pages </a>
		</p>
		-->

	<!-- Step 4 -->

		<p class="submit step-tour">
		<span class="step"><span class="number">4</span></span>
		<span class="step-body">
			<a href="http://goo.gl/Qwq2gc" class="button  button-primary" target="_blank">Keep it secure</a>
			<span class="step-description">Subscribe to our private e-mail updates.<br />
			Security updates &#8226; New features &#8226; Design releases</span>
		</span>
		</p>

	<?php /*
		<p class="submit step-tour">
		<span class="step"><span class="number">4</span></span>
		<span class="step-body">
			<a href="<?php echo esc_url( add_query_arg('theme_tour', 'true', admin_url('themes.php')) ); ?>" class="button  button-primary">Take a quick tour</a>
			<span class="step-description">2 minutes interactive introduction<br />
			to our theme basic controls.  </span>
		</span>
		</p>
	*/?>

	<!-- other links -->

		<p class="submit step-support">
		<!-- <span class="step"><span class="number">4</span></span> -->
		<span class="step-body">
			GET SUPPORT: &nbsp; &nbsp;
			<a href="http://docs.lumbermandesigns.com/" target="_blank"><span class="dashicons dashicons-book"></span> <strong>Online Docs</strong></a>&nbsp; &nbsp;
			<a href="http://themeforest.net/item/seo-wp-social-media-and-digital-marketing-agency/8012838/support/contact/" target="_blank"><span class="dashicons dashicons-format-chat"></span> <strong>One to one support</strong></a>  &nbsp; &nbsp;

			OR SAY HELLO:  &nbsp; &nbsp; <a href="http://facebook.com/lumbermandesigns/" target="_blank"><span class="dashicons dashicons-facebook"></span></a>&nbsp; &nbsp;
			<a href="http://twitter.com/lumbermandesign/" target="_blank"><span class="dashicons dashicons-twitter"></span></a>&nbsp; &nbsp;
			<a href="http://instagram.com/lumbermandesigns/" target="_blank"><span class="dashicons dashicons-format-image"></span></a>&nbsp; &nbsp;
		</span>
		</p>

		<?php
			// Show 'hide' button only when the demo content imported
			if ( get_option( LBMN_THEME_NAME . '_democontent_imported') ):
		?>
		<p class="submit action-skip"> <a class="skip button-primary" href="<?php echo esc_url( add_query_arg('hide_quicksetup', 'true', admin_url('themes.php')) ); ?>">Hide this message</a></p>
		<?php
			endif;
		?>

	</div>
<a name="focus-after-installer" id="focus-after-installer">&nbsp;</a>
<style type="text/css">.theme-installer-spinner{display:none;}</style>
<style type="text/css">.lumberman-message.quick-setup{display:block;}</style>
</div>

<?php
} //function lbmn_setmessage_themeinstall()


function lbmn_themeinstaller_add_help() {

	// Prepare button hide/show theme setup panel
	if ( !get_option( LBMN_THEME_NAME . '_hide_quicksetup' ) ) {
		$action_button = '<a href="' . esc_url( add_query_arg('hide_quicksetup', 'true', admin_url('themes.php')) ).'">Hide</a>';
	} else {
		$action_button = '<a href="' . esc_url( add_query_arg('show_quicksetup', 'true', admin_url('themes.php')) ).'">Show</a>';
	}


	$screen = get_current_screen();
	//$screen->remove_help_tabs();
	$screen->add_help_tab( array(
		'id'       => 'my-plugin-default',
		'title'    => __( 'SEOWP Theme' ),
		'content'  => '<p><strong>Quick theme installer:</strong> <ul><li>' . $action_button .' theme setup options panel</li>'.
		'<li><a href="' . esc_url( add_query_arg('reset_quicksetup', 'true', admin_url('themes.php')) ).'">Reset</a> completed quick theme installer steps</a></li>'.
		'</ul></p>'.
		'<p><strong>Get help:</strong> <ul><li><a href="http://docs.lumbermandesigns.com" target="_blank">Online theme documentation</a></li>'.
		'<li><a href="http://themeforest.net/item/seo-wp-social-media-and-digital-marketing-agency/8012838/support" target="_blank">One to one support</a></li></p>'
	));
	//add more help tabs as needed with unique id's

	// Help sidebars are optional
	// $screen->set_help_sidebar(
	// 	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	// 	'<p><a href="http://wordpress.org/support/" target="_blank">' . _( 'Support Forums' ) . '</a></p>'
	// );
}
// add_action('admin_menu', 'my_admin_add_page');
//global $my_plugin_hook;
// if ( $my_plugin_hook ) {
	add_action( 'load-themes.php', 'lbmn_themeinstaller_add_help' );
// }
// add_action

/**
* ----------------------------------------------------------------------
* Start basic theme settings setup process
*/
add_action( 'admin_notices', 'pvt_wordpress_content_importer' );
function pvt_wordpress_content_importer() {
	$theme_dir = get_template_directory();

	if ( is_admin() && isset($_GET['importcontent']) ) {


		if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				include $class_wp_importer;
			}
		}
		if ( ! class_exists('pvt_WP_Import') ) {
			$class_wp_import = $theme_dir . '/inc/importer/wordpress-importer.php';
			if ( file_exists( $class_wp_import ) ) {
				include $class_wp_import;
			}
		}
		if ( class_exists( 'WP_Importer' ) && class_exists( 'pvt_WP_Import' ) ) {
			$importer = new pvt_WP_Import();
			$files_to_import = array();

			// Live Composer has links to images hard-coded, so before importing
			// media we need to check that the Settings > Media >
			// 'Organize my uploads into month- and year-based folders' unchecked
			// as on demo server. After import is done we set back original state
			// of this setting.
			$setting_original_useyearmonthfolders = get_option( 'uploads_use_yearmonth_folders');
			update_option( 'uploads_use_yearmonth_folders', 0 );

			if ( $_GET['importcontent'] == 'basic-templates' ) {
				$import_path = $theme_dir . '/design/basic-config/';
				$files_to_import[] = $import_path . 'seowp-templates.xml.gz';
				$files_to_import[] = $import_path . 'seowp-themefooters.xml.gz';
				$files_to_import[] = $import_path . 'seowp-systempagetemplates.xml.gz';
				$files_to_import[] = $import_path . 'seowp-livecomposer-tutorials.xml.gz';
			}

			if ( $_GET['importcontent'] == 'alldemocontent' ) {
				$import_path = $theme_dir . '/design/demo-content/';

				$files_array = array(
					array(
						'seowp-homepages.xml.gz',
						'seowp-predesignedpages-1.xml.gz',
					),
					array(
						'seowp-predesignedpages-2.xml.gz',
						'seowp-predesignedpages-3.xml.gz',
					),
					array(
						'seowp-predesignedpages-4.xml.gz',
						'seowp-predesignedpages-5.xml.gz',
					),
					array(
						'seowp-predesignedpages-6.xml.gz',
						'seowp-predesignedpages-7.xml.gz',
					),
					array(
						'seowp-predesignedpages-8.xml.gz',
						'seowp-predesignedpages-9.xml.gz',
					),
					array(
						'seowp-predesignedpages-10.xml.gz',
						'seowp-predesignedpages-11.xml.gz',
					),
					array(
						'seowp-downloads.xml.gz',
						'seowp-partners.xml.gz',
					),
					array(
						'seowp-staff.xml.gz',
						'seowp-testimonials.xml.gz',
					),
					array(
						'seowp-posts.xml.gz',
						'seowp-projects.xml.gz',
					),
					array( // 10
						'seowp-media-homepage.xml.gz',
					),
					array(
						'seowp-media-menuimages.xml.gz',
					),
					array(
						'seowp-media-sliderimages.xml.gz',
					),
					array(
						'seowp-media-clientlogos.xml.gz',
					),
					array(
						'seowp-media-blogpostthumbs.xml.gz',
					),
					array(
						'seowp-media-footerimages.xml.gz',
					),
					array( // 16
						'seowp-media-staffavatars.xml.gz',
					),
					array(
						'seowp-media-servicepage.xml.gz',
					),
					array(
						'seowp-media-sectionbackgrounds.xml.gz',
					),
					array(
						'seowp-media-ebookcovers.xml.gz',
					),
					array( // 20
						'seowp-media-projectthumbs.xml.gz',
					),
				);

				if( isset($_GET['importcontent_part']) ){
					foreach($files_array[$_GET['importcontent_part']] as $file_name){
						$files_to_import[] = $import_path . $file_name;
					}
					if(isset($files_array[($_GET['importcontent_part']+1)]))
						echo '<input type="hidden" name="importcontent_part" id="importcontent_part" value="'.($_GET['importcontent_part']+1).'" />';
				}
			}

			// Start Import

			if ( file_exists( $class_wp_importer ) ) {
				// Import included images
				$importer->fetch_attachments = true;

				foreach ($files_to_import as $import_file) {
					if( is_file($import_file) ) {
						ob_start();
							$importer->import( $import_file );

							$log = ob_get_contents();
						ob_end_clean();

						// output log in the hidden div
						echo '<div class="ajax-log">';
						echo $log;
						echo '</div>';


						if ( stristr($log, 'error') || !stristr($log, 'All done.') ) {
							// Set marker div that will be fildered by ajax request
							echo '<div class="ajax-request-error"></div>';

							// output log in the div
							echo '<div class="ajax-error-log">';
							echo $log;
							echo '</div>';
						}

					} else {
						// Set marker div that will be fildered by ajax request
						echo '<div class="ajax-request-error"></div>';

						// output log in the div
						echo '<div class="ajax-error-log">';
						echo "Can't open file: " . $import_file . "</ br>";
						echo '</div>';
					}
				}

			} else {
				// Set marker div that will be fildered by ajax request
				echo '<div class="ajax-request-error"></div>';

				// output log in the div
				echo '<div class="ajax-error-log">';
				echo "Failed to load: " . $class_wp_import . "</ br>";
				echo '</div>';
			}

			// Set 'Organize my uploads into month- and year-based folders' setting
			// to its original state
			update_option( 'uploads_use_yearmonth_folders', $setting_original_useyearmonthfolders );

		}

		/**
		 * ----------------------------------------------------------------------
		 * Basic configuration:
		 * Post import actions
		 */

		if ( $_GET['importcontent'] == 'basic-templates' ) {

			// 1. Import Menus
			// 2. Activate Mega Main Menu for menu locations
			// 3. Import Widgets
			// 4. Demo description for author
			// 5. Tutorial Pages for LiveComposer
			// 6. Newsletter Sign-Up Plugin Settings
			// 7. Rotating Tweets Default Options Setup
			// 8. Regenerate Custom CSS

			// Path to the folder with basic import files
			$import_path_basic_config = $theme_dir . '/design/basic-config/';

			// 1:
			// Import Top Bar menu
			// if no menu set for 'topbar' location
			if( !has_nav_menu('topbar') ) {
				if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
					wpfw_import_menu($import_path_basic_config . 'seowp-menu-topbar.txt', 'topbar');
				}
			}

			// Import Mega Main Menu menu
			// if no menu set for 'header-menu' location
			if( !has_nav_menu('header-menu') ) {
				if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
					wpfw_import_menu($import_path_basic_config . 'seowp-menu-megamainmenu.txt', 'header-menu');
				}
			}

			$locations = get_nav_menu_locations();
			set_theme_mod('nav_menu_locations', $locations);

			// Import Mobile Off-Canvas Menu
			if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
				wpfw_import_menu($import_path_basic_config . 'seowp-menu-mobile-offcanvas.txt');
			}

			// 2: Activate Mega Main Menu for 'topbar' and 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				lbmn_activate_mainmegamenu_locations ();
			}

			// Predefine Custom Sidebars in LiveComposer
			// First set new sidebars in options table
			update_option(
				'dslc_plugin_options_widgets_m',
				array(
					'sidebars' => 'Sidebar,404 Page Widgets,Comment Form Area,',
				)
			);

			// Define default Archive and Search options with System Templates

			// 404 Page Template
			$current_lc_archive_options = get_option('dslc_plugin_options_archives');
			$current_lc_archive_options['404_page'] = lbmn_get_page_by_title( LBMN_SYSTEMPAGE_404_DEFAULT, 'lbmn_archive' );

			// Archive Page Template
			$new_archive_listing_id = lbmn_get_page_by_title( LBMN_SYSTEMPAGE_ARCHIVE_DEFAULT, 'lbmn_archive' );
			$current_lc_archive_options['post'] = $new_archive_listing_id;
			$current_lc_archive_options['dslc_projects'] = $new_archive_listing_id;
			$current_lc_archive_options['dslc_galleries'] = $new_archive_listing_id;
			$current_lc_archive_options['dslc_downloads'] = $new_archive_listing_id;
			$current_lc_archive_options['dslc_staff'] = $new_archive_listing_id;
			$current_lc_archive_options['dslc_partners'] = $new_archive_listing_id;
			$current_lc_archive_options['author'] = $new_archive_listing_id;

			// Search Results
			$new_search_listing_id = lbmn_get_page_by_title( LBMN_SYSTEMPAGE_SEARCHRESULTS_DEFAULT, 'lbmn_archive' );
			$current_lc_archive_options['search_results'] = $new_search_listing_id;

			update_option( 'dslc_plugin_options_archives', $current_lc_archive_options );

			// Then run LiveComposer function that creates sidebars dynamically
			dslc_sidebars();

			// 3: Import widgets
			$files_with_widgets_to_import = array();
			$files_with_widgets_to_import[] = $import_path_basic_config . 'seowp-widgets.wie';

			// Remove default widgets from 'mobile-offcanvas' widget area
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			if (is_array($sidebars_widgets['mobile-offcanvas'])) {
				$sidebars_widgets['mobile-offcanvas'] = NULL;
			}
			update_option( 'sidebars_widgets', $sidebars_widgets );

			// There are dynamic values in 'seowp-widgets.wie' that needs to be replaced
			// before import processing
			global $widget_strings_replace;
			$widget_strings_replace = array(
				'TOREPLACE_OFFCANVAS_MENUID' => lbmn_get_menuid_by_menutitle ( 'Mobile Off-canvas Menu' ),
			);

			foreach ($files_with_widgets_to_import as $file) {
				pvt_import_data( $file );
			}

			// 4: Put some demo description into current user info field
			// that used in the blog user boxes
			$user_ID = get_current_user_id();
			$user_info = get_userdata( $user_ID );

			if ( !$user_info->description ) {
				update_user_meta(
					$user_ID,
					'description',
					'This is author biographical info, ' .
					'that can be used to tell more about you, your iterests, ' .
					'background and experience. ' .
					'You can change it on <a href="/wp-admin/profile.php">Admin &gt; Users &gt; Your Profile &gt; Biographical Info</a> page."'
				);
			}

			// 5: Predefine Tutorial Pages in LiveComposer
			update_option(
				'dslc_plugin_options_tuts',
				array(
					'lc_tut_chapter_one' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-1'),
					'lc_tut_chapter_two' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-2'),
					'lc_tut_chapter_three' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-3'),
					'lc_tut_chapter_four' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-4'),
				)
			);

			// 6: Newsletter Sign-Up Plugin Form Elements
			update_option(
				'nsu_form',
				array(
					'email_label' => '',
					'email_default_value' => 'Your email address...',
					'email_label' => '',
					'redirect_to' => get_site_url() . '/index.php?pagename=/lbmn_archive/thanks-for-signing-up/',
				)
			);

			// Add custom Mega Main Menu options
			$mmm_options = get_option( 'mega_main_menu_options' );

			// Add custom Additional Mega Menu styles
			$mmm_options['additional_styles_presets'] = array(
				'1' => array(
							'style_name' => "Call to action item",
							'text_color' => "rgba(255,255,255,1)",
							'font' => array(
														"font_size" => "15",
														"font_weight" => "600",
													),
							'icon' => array(
														"font_size" => "16",
													),
							'bg_gradient' => array(
														"color1" => "#A1C627",
														"start" => "0",
														"color2" => "#A1C627",
														"end" => "100",
														"orientation" => "top",
													),
							"text_color_hover" => "rgba(255,255,255,1)",
							"bg_gradient_hover" => array(
														"color1" => "#56AEE3",
														"start" => "0",
														"color2" => "#56AEE3",
														"end" => "100",
														"orientation" => "top",
													),
						 ),
				'2' => array(
							'style_name' => "Dropdown Heading",
							'text_color' => "rgba(0,0,0,1)",
							'font' => array(
														"font_size" => "15",
														"font_weight" => "400",
													),
							'icon' => array(
														"font_size" => "15",
													),
							'bg_gradient' => array(
														"color1" => "",
														"start" => "0",
														"color2" => "",
														"end" => "100",
														"orientation" => "top",
													),
							"text_color_hover" => "rgba(0,0,0,1)",
							"bg_gradient_hover" => array(
														"color1" => "",
														"start" => "0",
														"color2" => "",
														"end" => "100",
														"orientation" => "top",
													),
						 ),
				'3' => array(
							'style_name' => "Dropdown Menu Text",
							'text_color' => "rgba(0,0,0,1)",
							'icon' => array(
														"font_size" => "21",
													),
							'font' => array(
									"font_size" => "21",
									"font_weight" => "300",
								),
							'bg_gradient' => array(
									"color1" => "",
									"start" => "0",
									"color2" => "",
									"end" => "100",
									"orientation" => "top",
								),
							"text_color_hover" => "rgba(0,0,0,1)",
							"bg_gradient_hover" => array(
									"color1" => "",
									"start" => "0",
									"color2" => "",
									"end" => "100",
									"orientation" => "top",
								),
						 ),
			);

			// Add custom icons
			$mmm_options['set_of_custom_icons'] = array(
				'1' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-spain.png'),
						 ),
				'2' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-italy.png'),
						 ),
				'3' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-france.png'),
						 ),
				'4' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-uk.png'),
						 ),
				'5' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-us.png'),
						 ),
				'6' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-austria.png'),
						 ),
				'7' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-belgium.png'),
						 ),
				'8' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-germany.png'),
						 ),
				'9' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-netherlands.png'),
						 ),
				'10' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-poland.png'),
						 ),
				'11' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-portugal.png'),
						 ),
				'12' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-romania.png'),
						 ),
				'13' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-russia.png'),
						 ),
				'14' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-ukraine.png'),
						 ),
			);

			// Put Mega Main Menu options back
			update_option( 'mega_main_menu_options', $mmm_options );

			// 8: Regenerate Custom CSS
			lbmn_customized_css_cache_reset(false); // refresh custom css without printig css (false)

			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				// call the function that normaly starts only in Theme Customizer
				lbmn_mainmegamenu_customizer_integration();
			}
		} // if $_GET['importcontent']


		/**
		 * ----------------------------------------------------------------------
		 * Demo Content: Full
		 */

		if ( ( $_GET['importcontent'] == 'alldemocontent' ) && ( $_GET['importcontent_part'] == 16 ) ) {
			$import_path_demo_content = $theme_dir . '/design/demo-content/';

			// Import Demo Ninja Forms
			lbmn_ninjaforms_import();

			lbmn_debug_console( 'Import Demo Mega Menu' );
			// Import Demo Mega Menu menu
			if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
				wpfw_import_menu($import_path_demo_content . 'seowp-demomegamenu.txt', 'header-menu');
			}

			$locations = get_nav_menu_locations();
			set_theme_mod('nav_menu_locations', $locations);

			// Activate Mega Main Menu for 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				lbmn_activate_mainmegamenu_locations ();
			}

			// Import pre-designed MasterSlider Slides
			// Check if MasterSlider is active

			// http://support.averta.net/envato/support/ticket/regenerate-custom-css-programatically/#post-16478
			if ( defined('MSWP_AVERTA_VERSION') ) {

				$current_sliders = get_masterslider_names('title-id');
				$slider_already_imported = false;

				foreach ($current_sliders as $slider => $slider_id) {
					if ( stristr($slider, 'Flat Design Style')) {
						$slider_already_imported = true;
					}
				}

				if ( !$slider_already_imported ) {
					global $ms_importer;
					if( is_null( $ms_importer ) ) $ms_importer = new MSP_Importer();

					// * @return bool   true on success and false on failure
					$slider_import_state = $ms_importer->import_data( file_get_contents($import_path_demo_content . 'seowp-masterslider.json') );
				}

				// Force Master Slider Custom CSS regeneration
				include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/msp-admin-functions.php');

				if (function_exists('msp_save_custom_styles')) {
					msp_update_preset_css (); // Presets re-generation
					msp_save_custom_styles(); // Save sliders custom css
				}

			}

			// Use a static front page
			$home_page = get_page_by_title( LBMN_HOME_TITLE );
			update_option( 'page_on_front', $home_page->ID );
			update_option( 'show_on_front', 'page' );

			// Set the blog page (not needed)
			// $blog = get_page_by_title( LBMN_BLOG_TITLE );
			// update_option( 'page_for_posts', $blog->ID );

			lbmn_debug_console( 'lbmn_customized_css_cache_reset' );
			// Regenerate Custom CSS
			lbmn_customized_css_cache_reset(false); // refresh custom css without printig css (false)

			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				// call the function that normally starts only in Theme Customizer
				lbmn_mainmegamenu_customizer_integration();
			}

			// lbmn_debug_console( 'Search & Replace image URLS' );

			// Search & Replace image URLS
			// lbmn_lcsearchreplace(); not needed any more

		} // if $_GET['importcontent']

	} // is isset($_GET['importcontent'])
}

/**
* ----------------------------------------------------------------------
* Start a theme tour
*/

if ( is_admin() && isset($_GET['theme_tour'] ) && $pagenow == "themes.php" ) {
	// Register the pointer styles and scripts
	add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

	// Add pointer javascript
	add_action( 'admin_print_footer_scripts', 'add_pointer_scripts' );

	// enqueue javascripts and styles
	function enqueue_scripts()
	{
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	// Add the pointer javascript
	function add_pointer_scripts()
	{
		$pointer_content = '<h3>We use a theme customizer</h3>';
		$pointer_content .= '<p>All theme options available for customization in theme customizer.</p>';
	?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('#menu-appearance a[href="customize.php"]').pointer({
				// pointer_id: 'customizer_menu_link',
				content: '<?php echo $pointer_content; ?>',
				position: {
					 edge: 'left', //top, bottom, left, right
					 align: 'middle' //top, bottom, left, right, middle
				 },
				buttons: function( event, t ) {

					var $buttonClose = jQuery('<a class="button-secondary" style="margin-right:10px;" href="#">End Tour</a>');
					$buttonClose.bind( 'click.pointer', function() {

						t.element.pointer('close');
					});

					var buttons = $('<div class="tiptour-buttons">');
					buttons.append($buttonClose);
					buttons.append('<a class="button-primary" style="margin-right:10px;" href="<?php echo admin_url('customize.php#first-time-visit'); ?>">Go to Theme Customizer</a>');
					return buttons;
				},

				close: function() {
					// Once the close button is hit
					$.post( ajaxurl, {
						pointer: 'customizer_menu_link',
						action: 'dismiss-wp-pointer'
					});
				}
			}).pointer('open');

			$(".lumberman-message.quick-setup .step-tour").addClass("step-completed");
		});
		//]]>
		</script>
	<?php
	}
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}

/* Hide quick tour message block */
if ( is_admin() && isset($_GET['hide_quicksetup'] ) && $pagenow == "themes.php" ) {
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}

/* Show quick tour message block */
if ( is_admin() && isset($_GET['show_quicksetup'] ) && $pagenow == "themes.php" ) {
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', false ); // set option to not show quick setup block anymore
}


/**
 * ----------------------------------------------------------------------
 * Page redirects for LiveComposer Tutorials
 */
add_action( 'template_redirect', 'lbmn_lc_tutorial_redirect' );
function lbmn_lc_tutorial_redirect() {
	if(
		is_user_logged_in() && !isset($_GET['dslc']) &&
		( is_page( 'chapter-1' ) || is_page( 'chapter-2' ) || is_page( 'chapter-3' )  || is_page( 'chapter-4' ) )
	) {
		$arr_params = array( 'dslc' => 'active', 'dslc_tut' => 'start' );
		wp_redirect( esc_url( add_query_arg($arr_params, get_permalink()) ) );
		exit();
	}
}

/**
 * ----------------------------------------------------------------------
 * In some situations on theme switch WordPress forget menus
 * that assigned to menu locations
 *
 * The next code block remember [menu id > location] pairs before theme
 * switch and redeclare it when users activate our theme again
 */

add_action( 'current_screen', 'lbmn_save_menu_locations' );
function lbmn_save_menu_locations($current_screen)
{
	// If Apperance > Menu screen visited
	if ( $current_screen->id == 'nav-menus' ) {
		// Remember menus assigned to our locations
		$locations = get_nav_menu_locations();
		update_option( LBMN_THEME_NAME . '_menuid_topbar', $locations['topbar'] );
		update_option( LBMN_THEME_NAME . '_menuid_header', $locations['header-menu'] );
	}
}

add_action('after_switch_theme', 'lbmn_redeclare_menu_locations' );
function lbmn_redeclare_menu_locations () {

	// check if 'header' locaiton has no menu assigned
	$menuid_header = get_option( LBMN_THEME_NAME . '_menuid_header' );
	if( !has_nav_menu('header-menu') && isset($menuid_header) ) {
		// Attach saved before menu id to 'topbar' location
		$locations = get_nav_menu_locations();
		$locations['header-menu'] = $menuid_header;
		set_theme_mod('nav_menu_locations', $locations);
	}

	// check if 'topbar' locaiton has no menu assigned
	$menuid_topbar = get_option( LBMN_THEME_NAME . '_menuid_topbar' );
	if( !has_nav_menu('topbar') && isset($menuid_topbar) ) {
		// Attach saved before menu id to 'topbar' location
		$locations = get_nav_menu_locations();
		$locations['topbar'] = $menuid_topbar;
		set_theme_mod('nav_menu_locations', $locations);
	}
}

// Replace dynamic values of widgets import (called from widgets-importer.php)
function lbmn_strreplace_on_widgetsimport($data) {
	if ($data) {
		global $widget_strings_replace;
		foreach ($widget_strings_replace as $search => $replace) {
			$data = str_replace($search, $replace, $data);
		}
	}
	return $data;
}


/**
 * ----------------------------------------------------------------------
 * Ninja Forms Importer
 */

function lbmn_ninjaforms_import() {
	$import_path = get_template_directory() . '/design/demo-content/';

	lbmn_debug_console( 'Ninja Forms Import Started' );
	// Import demo forms for Ninja Forms Plugin
	if( is_plugin_active('ninja-forms/ninja-forms.php') ) {
		foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator($import_path) ) as $filename) {
			if ( !stristr($filename, '.') ) {
				if( is_plugin_active('ninja-forms/ninja-forms.php') ) {
					ninja_forms_import_form( file_get_contents($filename) );
				}

			}
		}
	}
}