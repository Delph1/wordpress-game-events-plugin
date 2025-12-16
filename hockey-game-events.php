<?php
/**
 * Plugin Name: Bunkersnack Game Manager
 * Plugin URI: https://github.com/delph1/bunkersnack-game-manager
 * Description: A WordPress plugin for tracking game events, player statistics, and displaying game summaries and player stats tables
 * Version: 1.5.3
 * Author: Andreas Galistel
 * Author URI: https://bunkersnack.se
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bunkersnack-game-manager
 * Domain Path: /languages
 *
 * @package BunkersnackGameManager
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'HGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HGE_PLUGIN_VERSION', '1.5.3' );

// Include core files
require_once HGE_PLUGIN_DIR . 'includes/class-hge-database.php';
require_once HGE_PLUGIN_DIR . 'includes/class-hge-admin.php';
require_once HGE_PLUGIN_DIR . 'includes/class-hge-shortcodes.php';
require_once HGE_PLUGIN_DIR . 'includes/class-hge-stats.php';

/**
 * Hockey Game Events Plugin Main Class
 */
class Hockey_Game_Events {

    /**
     * Plugin instance
     *
     * @var self
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return self
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Initialize plugin
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        HGE_Database::create_tables();
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain( 'bunkersnack-game-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

        // Initialize classes
        HGE_Database::init();
        HGE_Admin::init();
        HGE_Shortcodes::init();
        HGE_Stats::init();
    }
}

// Initialize plugin
Hockey_Game_Events::get_instance();
