<?php
/**
 * Admin Interface
 *
 * @package HockeyGameEvents
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hockey Game Events Admin Class
 */
class HGE_Admin {

    /**
     * Initialize admin
     */
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_scripts' ) );
        add_action( 'wp_ajax_hge_save_season', array( __CLASS__, 'ajax_save_season' ) );
        add_action( 'wp_ajax_hge_delete_season', array( __CLASS__, 'ajax_delete_season' ) );
        add_action( 'wp_ajax_hge_get_season', array( __CLASS__, 'ajax_get_season' ) );
        add_action( 'wp_ajax_hge_save_team', array( __CLASS__, 'ajax_save_team' ) );
        add_action( 'wp_ajax_hge_delete_team', array( __CLASS__, 'ajax_delete_team' ) );
        add_action( 'wp_ajax_hge_get_team', array( __CLASS__, 'ajax_get_team' ) );
        add_action( 'wp_ajax_hge_save_player', array( __CLASS__, 'ajax_save_player' ) );
        add_action( 'wp_ajax_hge_delete_player', array( __CLASS__, 'ajax_delete_player' ) );
        add_action( 'wp_ajax_hge_get_player', array( __CLASS__, 'ajax_get_player' ) );
        add_action( 'wp_ajax_hge_save_game', array( __CLASS__, 'ajax_save_game' ) );
        add_action( 'wp_ajax_hge_delete_game', array( __CLASS__, 'ajax_delete_game' ) );
        add_action( 'wp_ajax_hge_get_game', array( __CLASS__, 'ajax_get_game' ) );
        add_action( 'wp_ajax_hge_save_event', array( __CLASS__, 'ajax_save_event' ) );
        add_action( 'wp_ajax_hge_delete_event', array( __CLASS__, 'ajax_delete_event' ) );
        add_action( 'wp_ajax_hge_get_game_events', array( __CLASS__, 'ajax_get_game_events' ) );
    }

    /**
     * Add admin menu
     */
    public static function add_admin_menu() {
        add_menu_page(
            __( 'Hockey Events', 'bunkersnack-game-manager' ),
            __( 'Hockey Events', 'bunkersnack-game-manager' ),
            'manage_options',
            'bunkersnack-game-manager',
            array( __CLASS__, 'render_main_page' ),
            'dashicons-tickets',
            25
        );

        add_submenu_page(
            'bunkersnack-game-manager',
            __( 'Seasons', 'bunkersnack-game-manager' ),
            __( 'Seasons', 'bunkersnack-game-manager' ),
            'manage_options',
            'bunkersnack-game-manager-seasons',
            array( __CLASS__, 'render_seasons_page' )
        );

        add_submenu_page(
            'bunkersnack-game-manager',
            __( 'Teams', 'bunkersnack-game-manager' ),
            __( 'Teams', 'bunkersnack-game-manager' ),
            'manage_options',
            'bunkersnack-game-manager-teams',
            array( __CLASS__, 'render_teams_page' )
        );

        add_submenu_page(
            'bunkersnack-game-manager',
            __( 'Players', 'bunkersnack-game-manager' ),
            __( 'Players', 'bunkersnack-game-manager' ),
            'manage_options',
            'bunkersnack-game-manager-players',
            array( __CLASS__, 'render_players_page' )
        );

        add_submenu_page(
            'bunkersnack-game-manager',
            __( 'Games', 'bunkersnack-game-manager' ),
            __( 'Games', 'bunkersnack-game-manager' ),
            'manage_options',
            'bunkersnack-game-manager-games',
            array( __CLASS__, 'render_games_page' )
        );

        add_submenu_page(
            'bunkersnack-game-manager',
            __( 'Player Stats', 'bunkersnack-game-manager' ),
            __( 'Player Stats', 'bunkersnack-game-manager' ),
            'manage_options',
            'bunkersnack-game-manager-stats',
            array( __CLASS__, 'render_stats_page' )
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public static function enqueue_admin_scripts( $hook_suffix ) {
        if ( strpos( $hook_suffix, 'bunkersnack-game-manager' ) === false ) {
            return;
        }

        wp_enqueue_style( 'hge-admin', HGE_PLUGIN_URL . 'assets/css/admin.css' );
        wp_enqueue_script( 'hge-admin', HGE_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), HGE_PLUGIN_VERSION, true );

        wp_localize_script(
            'hge-admin',
            'hgeAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'hge_admin_nonce' ),
                'strings'  => array(
                    'confirm_delete' => __( 'Are you sure you want to delete this item?', 'bunkersnack-game-manager' ),
                    'saving'         => __( 'Saving...', 'bunkersnack-game-manager' ),
                    'saved'          => __( 'Saved successfully!', 'bunkersnack-game-manager' ),
                    'error'          => __( 'An error occurred.', 'bunkersnack-game-manager' ),
                ),
            )
        );
    }

    /**
     * Render main admin page
     */
    public static function render_main_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Bunkersnack Game Manager', 'bunkersnack-game-manager' ); ?></h1>
            <p><?php esc_html_e( 'Welcome to the Bunkersnack Game Manager plugin. Use the menu to manage teams, players, games, and events.', 'bunkersnack-game-manager' ); ?></p>
            
            <div class="hge-dashboard">
                <div class="hge-dashboard-card">
                    <h3><?php esc_html_e( 'Seasons', 'bunkersnack-game-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Manage hockey seasons and leagues.', 'bunkersnack-game-manager' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-seasons' ) ); ?>" class="button button-primary">
                        <?php esc_html_e( 'Manage Seasons', 'bunkersnack-game-manager' ); ?>
                    </a>
                </div>
                <div class="hge-dashboard-card">
                    <h3><?php esc_html_e( 'Teams', 'bunkersnack-game-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Manage your hockey teams.', 'bunkersnack-game-manager' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-teams' ) ); ?>" class="button button-primary">
                        <?php esc_html_e( 'Manage Teams', 'bunkersnack-game-manager' ); ?>
                    </a>
                </div>
                <div class="hge-dashboard-card">
                    <h3><?php esc_html_e( 'Players', 'bunkersnack-game-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Manage team players and their information.', 'bunkersnack-game-manager' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-players' ) ); ?>" class="button button-primary">
                        <?php esc_html_e( 'Manage Players', 'bunkersnack-game-manager' ); ?>
                    </a>
                </div>
                <div class="hge-dashboard-card">
                    <h3><?php esc_html_e( 'Games', 'bunkersnack-game-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Create and manage games and game events.', 'bunkersnack-game-manager' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-games' ) ); ?>" class="button button-primary">
                        <?php esc_html_e( 'Manage Games', 'bunkersnack-game-manager' ); ?>
                    </a>
                </div>
                <div class="hge-dashboard-card">
                    <h3><?php esc_html_e( 'Player Statistics', 'bunkersnack-game-manager' ); ?></h3>
                    <p><?php esc_html_e( 'View calculated player statistics for the season.', 'bunkersnack-game-manager' ); ?></p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-stats' ) ); ?>" class="button button-primary">
                        <?php esc_html_e( 'View Statistics', 'bunkersnack-game-manager' ); ?>
                    </a>
                </div>
            </div>

            <!-- Shortcodes Documentation -->
            <div style="margin-top: 40px;">
                <h2><?php esc_html_e( 'Using Shortcodes', 'bunkersnack-game-manager' ); ?></h2>
                <p><?php esc_html_e( 'Display game information and player statistics on your pages and posts using these shortcodes:', 'bunkersnack-game-manager' ); ?></p>

                <div class="hge-form-container">
                    <h3><?php esc_html_e( 'Game Summary Shortcode', 'bunkersnack-game-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Display a summary of a specific game with all events.', 'bunkersnack-game-manager' ); ?></p>
                    
                    <h4><?php esc_html_e( 'Usage:', 'bunkersnack-game-manager' ); ?></h4>
                    <pre style="background: #f5f5f5; padding: 10px; border-left: 3px solid #0073aa;"><code>[hge_game_summary game_id="1"]</code></pre>
                    
                    <h4><?php esc_html_e( 'Parameters:', 'bunkersnack-game-manager' ); ?></h4>
                    <ul style="margin-left: 20px;">
                        <li><strong>game_id</strong> (required) - <?php esc_html_e( 'The ID of the game to display', 'bunkersnack-game-manager' ); ?></li>
                    </ul>

                    <h4><?php esc_html_e( 'Example:', 'bunkersnack-game-manager' ); ?></h4>
                    <p><?php esc_html_e( 'To find a game ID, go to Games page and check the game ID in the table.', 'bunkersnack-game-manager' ); ?></p>
                </div>

                <div class="hge-form-container">
                    <h3><?php esc_html_e( 'Player Statistics Shortcode', 'bunkersnack-game-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Display player statistics table for a specific season.', 'bunkersnack-game-manager' ); ?></p>
                    
                    <h4><?php esc_html_e( 'Usage:', 'bunkersnack-game-manager' ); ?></h4>
                    <pre style="background: #f5f5f5; padding: 10px; border-left: 3px solid #0073aa;"><code>[hge_player_stats season="2024"]</code></pre>
                    
                    <h4><?php esc_html_e( 'Parameters:', 'bunkersnack-game-manager' ); ?></h4>
                    <ul style="margin-left: 20px;">
                        <li><strong>season</strong> (optional) - <?php esc_html_e( 'The season year (e.g., "2024"). If not specified, shows the first season.', 'bunkersnack-game-manager' ); ?></li>
                    </ul>

                    <h4><?php esc_html_e( 'Example:', 'bunkersnack-game-manager' ); ?></h4>
                    <pre style="background: #f5f5f5; padding: 10px; border-left: 3px solid #0073aa;"><code>[hge_player_stats season="2024"]</code></pre>
                </div>

                <div class="hge-form-container" style="background: #f0f7ff; border-left: 3px solid #0073aa;">
                    <h3><?php esc_html_e( 'Tips', 'bunkersnack-game-manager' ); ?></h3>
                    <ul style="margin-left: 20px;">
                        <li><?php esc_html_e( 'Game summaries show all events (goals, assists, penalties) organized by period', 'bunkersnack-game-manager' ); ?></li>
                        <li><?php esc_html_e( 'Player statistics are automatically calculated from game events', 'bunkersnack-game-manager' ); ?></li>
                        <li><?php esc_html_e( 'You can add these shortcodes to any page or post', 'bunkersnack-game-manager' ); ?></li>
                        <li><?php esc_html_e( 'Statistics update automatically when you add or modify game events', 'bunkersnack-game-manager' ); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render seasons admin page
     */
    public static function render_seasons_page() {
        $seasons = HGE_Database::get_all_seasons_list();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Seasons', 'bunkersnack-game-manager' ); ?></h1>
            
            <!-- Quick Navigation Links -->
            <div class="hge-quick-nav" style="margin-bottom: 20px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager' ) ); ?>" class="button">← Dashboard</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-teams' ) ); ?>" class="button">Teams</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-players' ) ); ?>" class="button">Players</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-games' ) ); ?>" class="button">Games</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-stats' ) ); ?>" class="button">Statistics</a>
            </div>

            <div id="hge-season-form-container" class="hge-form-container">
                <h2><?php esc_html_e( 'Add/Edit Season', 'bunkersnack-game-manager' ); ?></h2>
                <form id="hge-season-form">
                    <input type="hidden" id="hge-season-id" name="id" value="0">
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="hge-season-name"><?php esc_html_e( 'Season Name', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-season-name" name="name" required class="regular-text" placeholder="<?php esc_attr_e( 'e.g., Elitserien 76/77', 'bunkersnack-game-manager' ); ?>"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-season-description"><?php esc_html_e( 'Description', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><textarea id="hge-season-description" name="description" class="large-text" rows="4"></textarea></td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Season', 'bunkersnack-game-manager' ); ?></button>
                        <button type="button" id="hge-season-reset" class="button"><?php esc_html_e( 'Clear Form', 'bunkersnack-game-manager' ); ?></button>
                    </p>
                </form>
            </div>

            <div class="hge-list-container">
                <h2><?php esc_html_e( 'All Seasons', 'bunkersnack-game-manager' ); ?></h2>
                <?php if ( ! empty( $seasons ) ) : ?>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Season Name', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Description', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'bunkersnack-game-manager' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $seasons as $season ) : ?>
                                <tr data-season-id="<?php echo intval( $season->id ); ?>">
                                    <td><?php echo esc_html( $season->name ); ?></td>
                                    <td><?php echo wp_kses_post( wp_trim_words( $season->description, 20 ) ); ?></td>
                                    <td>
                                        <button type="button" class="button hge-edit-season" data-season-id="<?php echo intval( $season->id ); ?>">
                                            <?php esc_html_e( 'Edit', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                        <button type="button" class="button button-link-delete hge-delete-season" data-season-id="<?php echo intval( $season->id ); ?>">
                                            <?php esc_html_e( 'Delete', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php esc_html_e( 'No seasons found. Create one using the form above.', 'bunkersnack-game-manager' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render teams admin page
     */
    public static function render_teams_page() {
        $teams = HGE_Database::get_all_teams();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Teams', 'bunkersnack-game-manager' ); ?></h1>
            
            <!-- Quick Navigation Links -->
            <div class="hge-quick-nav" style="margin-bottom: 20px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager' ) ); ?>" class="button">← Dashboard</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-players' ) ); ?>" class="button">Players</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-games' ) ); ?>" class="button">Games</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-stats' ) ); ?>" class="button">Statistics</a>
            </div>

            <div id="hge-team-form-container" class="hge-form-container">
                <h2><?php esc_html_e( 'Add/Edit Team', 'bunkersnack-game-manager' ); ?></h2>
                <form id="hge-team-form">
                    <input type="hidden" id="hge-team-id" name="id" value="0">
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="hge-team-name"><?php esc_html_e( 'Team Name', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-team-name" name="name" required class="regular-text" placeholder="<?php esc_attr_e( 'e.g., Örebro Ishockeyklubb', 'bunkersnack-game-manager' ); ?>"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-team-shortcode"><?php esc_html_e( 'Team Shortcode', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-team-shortcode" name="shortcode" class="regular-text" placeholder="<?php esc_attr_e( 'e.g., ÖIK', 'bunkersnack-game-manager' ); ?>"></td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Team', 'bunkersnack-game-manager' ); ?></button>
                        <button type="button" id="hge-team-reset" class="button"><?php esc_html_e( 'Clear Form', 'bunkersnack-game-manager' ); ?></button>
                    </p>
                </form>
            </div>

            <div class="hge-list-container">
                <h2><?php esc_html_e( 'All Teams', 'bunkersnack-game-manager' ); ?></h2>
                <?php if ( ! empty( $teams ) ) : ?>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Team Name', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Shortcode', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'bunkersnack-game-manager' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $teams as $team ) : ?>
                                <tr data-team-id="<?php echo intval( $team->id ); ?>">
                                    <td><?php echo esc_html( $team->name ); ?></td>
                                    <td><?php echo esc_html( $team->shortcode ); ?></td>
                                    <td>
                                        <button type="button" class="button hge-edit-team" data-team-id="<?php echo intval( $team->id ); ?>">
                                            <?php esc_html_e( 'Edit', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                        <button type="button" class="button button-link-delete hge-delete-team" data-team-id="<?php echo intval( $team->id ); ?>">
                                            <?php esc_html_e( 'Delete', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php esc_html_e( 'No teams found. Create one using the form above.', 'bunkersnack-game-manager' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render players admin page
     */
    public static function render_players_page() {
        $players = HGE_Database::get_all_players();
        $teams = HGE_Database::get_all_teams();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Players', 'bunkersnack-game-manager' ); ?></h1>
            
            <!-- Quick Navigation Links -->
            <div class="hge-quick-nav" style="margin-bottom: 20px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager' ) ); ?>" class="button">← Dashboard</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-teams' ) ); ?>" class="button">Teams</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-games' ) ); ?>" class="button">Games</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-stats' ) ); ?>" class="button">Statistics</a>
            </div>

            <div id="hge-player-form-container" class="hge-form-container">
                <h2><?php esc_html_e( 'Add/Edit Player', 'bunkersnack-game-manager' ); ?></h2>
                <form id="hge-player-form">
                    <input type="hidden" id="hge-player-id" name="id" value="0">
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="hge-player-team"><?php esc_html_e( 'Team', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-player-team" name="team_id" class="regular-text">
                                        <option value="">-- Select Team --</option>
                                        <?php foreach ( $teams as $team ) : ?>
                                            <option value="<?php echo intval( $team->id ); ?>">
                                                <?php echo esc_html( $team->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-player-name"><?php esc_html_e( 'Name', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-player-name" name="name" required class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-player-number"><?php esc_html_e( 'Jersey Number', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="number" id="hge-player-number" name="number" class="small-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-player-position"><?php esc_html_e( 'Position', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-player-position" name="position" class="regular-text">
                                        <option value="">-- Select --</option>
                                        <option value="C"><?php esc_html_e( 'Center', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="LW"><?php esc_html_e( 'Left Wing', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="RW"><?php esc_html_e( 'Right Wing', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="D"><?php esc_html_e( 'Defenseman', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="G"><?php esc_html_e( 'Goalie', 'bunkersnack-game-manager' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-player-goalie"><?php esc_html_e( 'Goalie', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="checkbox" id="hge-player-goalie" name="is_goalie" value="1"></td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Player', 'bunkersnack-game-manager' ); ?></button>
                        <button type="button" id="hge-player-reset" class="button"><?php esc_html_e( 'Clear Form', 'bunkersnack-game-manager' ); ?></button>
                    </p>
                </form>
            </div>

            <div class="hge-list-container">
                <h2><?php esc_html_e( 'All Players', 'bunkersnack-game-manager' ); ?></h2>
                <?php if ( ! empty( $players ) ) : ?>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Team', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Name', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Number', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Position', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Type', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'bunkersnack-game-manager' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $players as $player ) : ?>
                                <tr data-player-id="<?php echo intval( $player->id ); ?>">
                                    <td><?php echo esc_html( $player->team_name ); ?></td>
                                    <td><?php echo esc_html( $player->name ); ?></td>
                                    <td><?php echo esc_html( $player->number ); ?></td>
                                    <td><?php echo esc_html( $player->position ); ?></td>
                                    <td><?php echo esc_html( $player->is_goalie ? 'Goalie' : 'Skater' ); ?></td>
                                    <td>
                                        <button type="button" class="button hge-edit-player" data-player-id="<?php echo intval( $player->id ); ?>">
                                            <?php esc_html_e( 'Edit', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                        <button type="button" class="button button-link-delete hge-delete-player" data-player-id="<?php echo intval( $player->id ); ?>">
                                            <?php esc_html_e( 'Delete', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php esc_html_e( 'No players found. Create one using the form above.', 'bunkersnack-game-manager' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render games admin page
     */
    public static function render_games_page() {
        $games = HGE_Database::get_all_games();
        $players = HGE_Database::get_all_players();
        $teams = HGE_Database::get_all_teams();
        $seasons_list = HGE_Database::get_all_seasons_list();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Games', 'bunkersnack-game-manager' ); ?></h1>
            
            <!-- Quick Navigation Links -->
            <div class="hge-quick-nav" style="margin-bottom: 20px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager' ) ); ?>" class="button">← Dashboard</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-seasons' ) ); ?>" class="button">Seasons</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-teams' ) ); ?>" class="button">Teams</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-players' ) ); ?>" class="button">Players</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-stats' ) ); ?>" class="button">Statistics</a>
            </div>

            <div id="hge-game-form-container" class="hge-form-container">
                <h2><?php esc_html_e( 'Add/Edit Game', 'bunkersnack-game-manager' ); ?></h2>
                <form id="hge-game-form">
                    <input type="hidden" id="hge-game-id" name="id" value="0">
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="hge-game-season"><?php esc_html_e( 'Season', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-game-season" name="season" class="regular-text">
                                        <option value="">-- Select Season --</option>
                                        <?php foreach ( $seasons_list as $season ) : ?>
                                            <option value="<?php echo esc_attr( $season->name ); ?>"><?php echo esc_html( $season->name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-game-date"><?php esc_html_e( 'Date', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="date" id="hge-game-date" name="game_date" required class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-game-home-team"><?php esc_html_e( 'Home Team', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-game-home-team" name="home_team" class="regular-text">
                                        <option value="">Select team...</option>
                                        <?php foreach ( $teams as $team ) : ?>
                                            <option value="<?php echo esc_attr( $team->id ); ?>"><?php echo esc_html( $team->name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="button hge-quick-add-team" data-target="hge-game-home-team">+ <?php esc_html_e( 'New Team', 'bunkersnack-game-manager' ); ?></button>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-game-away-team"><?php esc_html_e( 'Away Team', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-game-away-team" name="away_team" class="regular-text">
                                        <option value="">Select team...</option>
                                        <?php foreach ( $teams as $team ) : ?>
                                            <option value="<?php echo esc_attr( $team->id ); ?>"><?php echo esc_html( $team->name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="button hge-quick-add-team" data-target="hge-game-away-team">+ <?php esc_html_e( 'New Team', 'bunkersnack-game-manager' ); ?></button>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-game-location"><?php esc_html_e( 'Location', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-game-location" name="location" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-game-attendance"><?php esc_html_e( 'Attendance', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="number" id="hge-game-attendance" name="attendance" min="0" class="small-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-game-home-score"><?php esc_html_e( 'Home Score', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="number" id="hge-game-home-score" name="home_score" min="0" class="small-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-game-away-score"><?php esc_html_e( 'Away Score', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="number" id="hge-game-away-score" name="away_score" min="0" class="small-text"></td>
                            </tr>

                            <!-- Shot and Goal Statistics -->
                            <tr>
                                <th colspan="2"><strong><?php esc_html_e( 'Shot & Goal Statistics', 'bunkersnack-game-manager' ); ?></strong></th>
                            </tr>
                            <tr>
                                <th><label><?php esc_html_e( 'Home Shots by Period', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <label>P1: <input type="number" name="home_shots_p1" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>P2: <input type="number" name="home_shots_p2" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>P3: <input type="number" name="home_shots_p3" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>OT: <input type="number" name="home_shots_ot" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>PS: <input type="number" name="home_shots_ps" min="0" class="small-text" style="width: 60px;"></label>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php esc_html_e( 'Away Shots by Period', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <label>P1: <input type="number" name="away_shots_p1" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>P2: <input type="number" name="away_shots_p2" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>P3: <input type="number" name="away_shots_p3" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>OT: <input type="number" name="away_shots_ot" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>PS: <input type="number" name="away_shots_ps" min="0" class="small-text" style="width: 60px;"></label>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php esc_html_e( 'Home Goals by Period', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <label>P1: <input type="number" name="home_goals_p1" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>P2: <input type="number" name="home_goals_p2" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>P3: <input type="number" name="home_goals_p3" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>OT: <input type="number" name="home_goals_ot" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>PS: <input type="number" name="home_goals_ps" min="0" class="small-text" style="width: 60px;"></label>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php esc_html_e( 'Away Goals by Period', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <label>P1: <input type="number" name="away_goals_p1" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>P2: <input type="number" name="away_goals_p2" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>P3: <input type="number" name="away_goals_p3" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>OT: <input type="number" name="away_goals_ot" min="0" class="small-text" style="width: 60px;"></label>
                                    <label>PS: <input type="number" name="away_goals_ps" min="0" class="small-text" style="width: 60px;"></label>
                                </td>
                            </tr>

                            <tr>
                                <th><label for="hge-game-notes"><?php esc_html_e( 'Notes', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><textarea id="hge-game-notes" name="notes" class="large-text" rows="4"></textarea></td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Game', 'bunkersnack-game-manager' ); ?></button>
                        <button type="button" id="hge-game-reset" class="button"><?php esc_html_e( 'Clear Form', 'bunkersnack-game-manager' ); ?></button>
                    </p>
                </form>
            </div>

            <div class="hge-list-container">
                <h2><?php esc_html_e( 'All Games', 'bunkersnack-game-manager' ); ?></h2>
                <?php if ( ! empty( $games ) ) : ?>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'ID', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Season', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Date', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Home Team', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Away Team', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Score', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Location', 'bunkersnack-game-manager' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'bunkersnack-game-manager' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $games as $game ) : ?>
                                <tr data-game-id="<?php echo intval( $game->id ); ?>">
                                    <td><strong><?php echo intval( $game->id ); ?></strong></td>
                                    <td><?php echo esc_html( $game->season ); ?></td>
                                    <td><?php echo esc_html( date_i18n( 'Y-m-d', strtotime( $game->game_date ) ) ); ?></td>
                                    <td><?php echo esc_html( $game->home_team_name ?: 'N/A' ); ?></td>
                                    <td><?php echo esc_html( $game->away_team_name ?: 'N/A' ); ?></td>
                                    <td>
                                        <?php
                                        if ( ! is_null( $game->home_score ) && ! is_null( $game->away_score ) ) {
                                            echo esc_html( $game->home_score . ' - ' . $game->away_score );
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo esc_html( $game->location ); ?></td>
                                    <td>
                                        <button type="button" class="button hge-edit-game" data-game-id="<?php echo intval( $game->id ); ?>">
                                            <?php esc_html_e( 'Edit', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                        <button type="button" class="button hge-manage-events" data-game-id="<?php echo intval( $game->id ); ?>">
                                            <?php esc_html_e( 'Events', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                        <button type="button" class="button button-link-delete hge-delete-game" data-game-id="<?php echo intval( $game->id ); ?>">
                                            <?php esc_html_e( 'Delete', 'bunkersnack-game-manager' ); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php esc_html_e( 'No games found. Create one using the form above.', 'bunkersnack-game-manager' ); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Events Modal -->
        <div id="hge-events-modal" class="hge-modal" style="display:none;">
            <div class="hge-modal-content">
                <span class="hge-close">&times;</span>
                <h2 id="hge-events-modal-title"></h2>
                
                <h3><?php esc_html_e( 'Add Event', 'bunkersnack-game-manager' ); ?></h3>
                <form id="hge-event-form">
                    <input type="hidden" id="hge-event-game-id" name="game_id" value="0">
                    <input type="hidden" id="hge-event-id" name="id" value="0">
                    
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="hge-event-type"><?php esc_html_e( 'Event Type', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-event-type" name="event_type" required>
                                        <option value="">-- Select --</option>
                                        <option value="goal"><?php esc_html_e( 'Goal', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="assist"><?php esc_html_e( 'Assist', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="penalty"><?php esc_html_e( 'Penalty', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="shot_against"><?php esc_html_e( 'Shot Against', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="goal_allowed"><?php esc_html_e( 'Goal Allowed', 'bunkersnack-game-manager' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-event-player"><?php esc_html_e( 'Player', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-event-player" name="player_id">
                                        <option value="">-- Select Player --</option>
                                        <?php foreach ( $players as $player ) : ?>
                                            <option value="<?php echo intval( $player->id ); ?>">
                                                <?php echo esc_html( $player->name . ' #' . $player->number ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="button hge-quick-add-player" data-target="hge-event-player">+ <?php esc_html_e( 'New Player', 'bunkersnack-game-manager' ); ?></button>
                                </td>
                            </tr>
                            <tr id="hge-assists-row" style="display:none;">
                                <th><label for="hge-event-assists"><?php esc_html_e( 'Assists', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-event-assists" name="assists[]" multiple>
                                        <option value="">-- Select Players --</option>
                                        <?php foreach ( $players as $player ) : ?>
                                            <option value="<?php echo intval( $player->id ); ?>">
                                                <?php echo esc_html( $player->name . ' #' . $player->number ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Hold Ctrl/Cmd to select multiple players', 'bunkersnack-game-manager' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-event-period"><?php esc_html_e( 'Period', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-event-period" name="period" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4"><?php esc_html_e( 'OT', 'bunkersnack-game-manager' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-event-time"><?php esc_html_e( 'Time (mm:ss)', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-event-time" name="event_time" placeholder="mm:ss" class="small-text" style="width: 80px;"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-event-description"><?php esc_html_e( 'Description', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><textarea id="hge-event-description" name="description" class="large-text" rows="3"></textarea></td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Event', 'bunkersnack-game-manager' ); ?></button>
                        <button type="button" id="hge-event-reset" class="button"><?php esc_html_e( 'Clear Form', 'bunkersnack-game-manager' ); ?></button>
                    </p>
                </form>

                <h3><?php esc_html_e( 'Game Events', 'bunkersnack-game-manager' ); ?></h3>
                <div id="hge-events-list"></div>
            </div>
        </div>

        <script>
        console.log("Inline script test - this should always appear");
        
        // Direct event listener for assists field
        jQuery(document).ready(function($) {
            console.log("jQuery ready in inline script");
            
            // Handle manage events button click
            $(document).on("click", ".hge-manage-events", function() {
                const gameId = $(this).data("game-id");
                console.log("Manage events clicked for game: " + gameId);
                openEventsModal(gameId);
            });
            
            $(document).on("change", "#hge-event-type", function() {
                if ($(this).val() === "goal") {
                    $("#hge-assists-row").css("display", "table-row");
                } else {
                    $("#hge-assists-row").css("display", "none");
                }
            });
            
            // Load game details when modal opens
            function loadGameDetails(gameId) {
                $.ajax({
                    type: "GET",
                    url: hgeAdmin.ajax_url + "?action=hge_get_game&id=" + gameId,
                    dataType: "json",
                    success: function (response) {
                        console.log("Game data response: ", response);
                        if (response.success) {
                            const game = response.data;
                            
                            const homeTeam = game.home_team_name || 'Home Team';
                            const awayTeam = game.away_team_name || 'Away Team';
                            const title = game.game_date + " " + homeTeam + " vs " + awayTeam;
                            
                            
                            $("#hge-events-modal-title").text(title);
                            
                        }
                    }
                });
            }
            
            // Load game events list
            function loadGameEvents(gameId) {
                $.ajax({
                    type: "GET",
                    url: hgeAdmin.ajax_url + "?action=hge_get_game_events&id=" + gameId,
                    dataType: "json",
                    success: function (response) {
                        if (response.success && response.data) {
                            const events = response.data;
                            let html = "<ul>";
                            const assistsByGoal = {};
                            
                            events.forEach(function (event) {
                                if (event.event_type === 'assist' && event.parent_event_id) {
                                    if (!assistsByGoal[event.parent_event_id]) {
                                        assistsByGoal[event.parent_event_id] = [];
                                    }
                                    assistsByGoal[event.parent_event_id].push(event.name);
                                }
                            });
                            
                            events.forEach(function (event) {
                                if (event.event_type === 'assist') {
                                    return;
                                }
                                html += "<li>";
                                
                                // Convert seconds to mm:ss format
                                let timeDisplay = "0:00";
                                if (event.event_time) {
                                    const totalSeconds = parseInt(event.event_time, 10);
                                    // If the value is greater than 120, assume it's already in seconds
                                    // Otherwise, assume it's in minutes (old format)
                                    const isSeconds = totalSeconds > 120;
                                    const secondsToUse = isSeconds ? totalSeconds : (totalSeconds * 60);
                                    const minutes = Math.floor(secondsToUse / 60);
                                    const seconds = secondsToUse % 60;
                                    timeDisplay = minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
                                }
                                
                                html += "P" + event.period + " " + timeDisplay + " - " + event.event_type;
                                if (event.name) {
                                    html += " (" + event.name + ")";
                                }
                                if (event.event_type === 'goal' && assistsByGoal[event.id] && assistsByGoal[event.id].length > 0) {
                                    html += " - Assists: " + assistsByGoal[event.id].join(", ");
                                }
                                html += ' <button class="button button-link-delete hge-delete-single-event" data-event-id="' + event.id + '">Delete</button>';
                                html += "</li>";
                            });
                            html += "</ul>";
                            $("#hge-events-list").html(html);
                            
                            $(".hge-delete-single-event").on("click", function () {
                                deleteSingleEvent($(this).data("event-id"), gameId);
                            });
                        } else {
                            $("#hge-events-list").html("<p>No events yet.</p>");
                        }
                    }
                });
            }
            
            function deleteSingleEvent(eventId, gameId) {
                if (confirm("Are you sure?")) {
                    $.ajax({
                        type: "POST",
                        url: hgeAdmin.ajax_url,
                        data: {
                            action: "hge_delete_event",
                            id: eventId,
                            nonce: hgeAdmin.nonce,
                        },
                        success: function (response) {
                            if (response.success) {
                                loadGameEvents(gameId);
                            }
                        },
                    });
                }
            }
            
            function openEventsModal(gameId) {
                console.log("openEventsModal called with gameId: " + gameId);
                $("#hge-event-game-id").val(gameId);
                $("#hge-events-modal").show();
                loadGameEvents(gameId);
                loadGameDetails(gameId);
            }
        });
        </script>

        <!-- Quick Add Team Modal -->
        <div id="hge-quick-add-team-modal" class="hge-modal" style="display:none;">
            <div class="hge-modal-content">
                <span class="hge-close">&times;</span>
                <h2><?php esc_html_e( 'Quick Add Team', 'bunkersnack-game-manager' ); ?></h2>
                <form id="hge-quick-team-form">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="hge-quick-team-name"><?php esc_html_e( 'Team Name', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-quick-team-name" name="name" required class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-quick-team-shortcode"><?php esc_html_e( 'Shortcode', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-quick-team-shortcode" name="shortcode" class="regular-text"></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Team', 'bunkersnack-game-manager' ); ?></button>
                        <button type="button" class="button hge-close-modal"><?php esc_html_e( 'Cancel', 'bunkersnack-game-manager' ); ?></button>
                    </p>
                </form>
            </div>
        </div>

        <!-- Quick Add Player Modal -->
        <div id="hge-quick-add-player-modal" class="hge-modal" style="display:none;">
            <div class="hge-modal-content">
                <span class="hge-close">&times;</span>
                <h2><?php esc_html_e( 'Quick Add Player', 'bunkersnack-game-manager' ); ?></h2>
                <form id="hge-quick-player-form">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th><label for="hge-quick-player-name"><?php esc_html_e( 'Player Name', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="text" id="hge-quick-player-name" name="name" required class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-quick-player-number"><?php esc_html_e( 'Number', 'bunkersnack-game-manager' ); ?></label></th>
                                <td><input type="number" id="hge-quick-player-number" name="number" min="1" max="99" class="small-text"></td>
                            </tr>
                            <tr>
                                <th><label for="hge-quick-player-position"><?php esc_html_e( 'Position', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-quick-player-position" name="position">
                                        <option value="">-- Select Position --</option>
                                        <option value="Center"><?php esc_html_e( 'Center', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="Left Wing"><?php esc_html_e( 'Left Wing', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="Right Wing"><?php esc_html_e( 'Right Wing', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="Defenseman"><?php esc_html_e( 'Defenseman', 'bunkersnack-game-manager' ); ?></option>
                                        <option value="Goalie"><?php esc_html_e( 'Goalie', 'bunkersnack-game-manager' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hge-quick-player-team"><?php esc_html_e( 'Team', 'bunkersnack-game-manager' ); ?></label></th>
                                <td>
                                    <select id="hge-quick-player-team" name="team_id">
                                        <option value="">-- Select Team --</option>
                                        <?php foreach ( $teams as $team ) : ?>
                                            <option value="<?php echo esc_attr( $team->id ); ?>"><?php echo esc_html( $team->name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                        <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Player', 'bunkersnack-game-manager' ); ?></button>
                        <button type="button" class="button hge-close-modal"><?php esc_html_e( 'Cancel', 'bunkersnack-game-manager' ); ?></button>
                    </p>
                </form>
            </div>
        </div>

        <script>
        // Quick add functionality
        jQuery(document).ready(function($) {
            let currentTargetSelect = null;
            
            // Quick add team button
            $(document).on("click", ".hge-quick-add-team", function(e) {
                e.preventDefault();
                currentTargetSelect = $(this).data("target");
                $("#hge-quick-add-team-modal").show();
            });
            
            // Quick add player button
            $(document).on("click", ".hge-quick-add-player", function(e) {
                e.preventDefault();
                currentTargetSelect = $(this).data("target");
                $("#hge-quick-add-player-modal").show();
            });
            
            // Close modals
            $(document).on("click", ".hge-close-modal, #hge-quick-add-team-modal .hge-close, #hge-quick-add-player-modal .hge-close", function() {
                $("#hge-quick-add-team-modal").hide();
                $("#hge-quick-add-player-modal").hide();
            });
            
            // Save quick team
            $("#hge-quick-team-form").on("submit", function(e) {
                e.preventDefault();
                const name = $("#hge-quick-team-name").val();
                const shortcode = $("#hge-quick-team-shortcode").val();
                
                $.ajax({
                    type: "POST",
                    url: hgeAdmin.ajax_url,
                    data: {
                        action: "hge_save_team",
                        name: name,
                        shortcode: shortcode,
                        nonce: hgeAdmin.nonce,
                    },
                    success: function(response) {
                        if (response.success) {
                            const teamId = response.data.id;
                            const teamName = response.data.name;
                            
                            // Add option to target select
                            if (currentTargetSelect) {
                                $("#" + currentTargetSelect).append(
                                    $("<option></option>").attr("value", teamId).text(teamName)
                                ).val(teamId);
                            }
                            
                            // Reset and close
                            $("#hge-quick-team-form")[0].reset();
                            $("#hge-quick-add-team-modal").hide();
                            alert("Team created successfully!");
                        }
                    }
                });
            });
            
            // Save quick player
            $("#hge-quick-player-form").on("submit", function(e) {
                e.preventDefault();
                const name = $("#hge-quick-player-name").val();
                const number = $("#hge-quick-player-number").val();
                const position = $("#hge-quick-player-position").val();
                const teamId = $("#hge-quick-player-team").val();
                
                $.ajax({
                    type: "POST",
                    url: hgeAdmin.ajax_url,
                    data: {
                        action: "hge_save_player",
                        name: name,
                        number: number,
                        position: position,
                        team_id: teamId,
                        nonce: hgeAdmin.nonce,
                    },
                    success: function(response) {
                        if (response.success) {
                            const playerId = response.data.id;
                            const playerDisplay = name + (number ? " #" + number : "");
                            
                            // Add option to target select
                            if (currentTargetSelect) {
                                $("#" + currentTargetSelect).append(
                                    $("<option></option>").attr("value", playerId).text(playerDisplay)
                                ).val(playerId);
                            }
                            
                            // Reset and close
                            $("#hge-quick-player-form")[0].reset();
                            $("#hge-quick-add-player-modal").hide();
                            alert("Player created successfully!");
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Render player stats admin page
     */
    public static function render_stats_page() {
        $seasons = HGE_Database::get_all_seasons();
        $current_season = ! empty( $seasons ) ? $seasons[0] : null;
        $stats = $current_season ? HGE_Stats::get_season_stats( $current_season, array( 'orderby' => 'goals', 'order' => 'DESC' ) ) : array();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Player Statistics', 'bunkersnack-game-manager' ); ?></h1>
            <p><?php esc_html_e( 'Automatically calculated player statistics based on game events.', 'bunkersnack-game-manager' ); ?></p>
            
            <!-- Quick Navigation Links -->
            <div class="hge-quick-nav" style="margin-bottom: 20px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager' ) ); ?>" class="button">← Dashboard</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-teams' ) ); ?>" class="button">Teams</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-players' ) ); ?>" class="button">Players</a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=bunkersnack-game-manager-games' ) ); ?>" class="button">Games</a>
            </div>

            <?php if ( ! empty( $seasons ) ) : ?>
                <div class="hge-season-filter">
                    <label for="hge-season-select"><?php esc_html_e( 'Select Season:', 'bunkersnack-game-manager' ); ?></label>
                    <select id="hge-season-select">
                        <?php foreach ( $seasons as $season ) : ?>
                            <option value="<?php echo esc_attr( $season ); ?>" <?php selected( $season, $current_season ); ?>>
                                <?php echo esc_html( $season ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <table class="wp-list-table widefat striped" id="hge-stats-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Team', 'bunkersnack-game-manager' ); ?></th>
                            <th><?php esc_html_e( 'Player', 'bunkersnack-game-manager' ); ?></th>
                            <th><?php esc_html_e( 'Position', 'bunkersnack-game-manager' ); ?></th>
                            <th><?php esc_html_e( 'GP', 'bunkersnack-game-manager' ); ?></th>
                            <th><?php esc_html_e( 'Goals', 'bunkersnack-game-manager' ); ?></th>
                            <th><?php esc_html_e( 'Assists', 'bunkersnack-game-manager' ); ?></th>
                            <th><?php esc_html_e( 'PIM', 'bunkersnack-game-manager' ); ?></th>
                            <th><?php esc_html_e( 'Shots Against', 'bunkersnack-game-manager' ); ?></th>
                            <th><?php esc_html_e( 'Goals Allowed', 'bunkersnack-game-manager' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $stats ) ) : ?>
                            <?php foreach ( $stats as $stat ) : ?>
                                <tr class="<?php echo esc_attr( $stat->is_goalie ? 'hge-goalie' : 'hge-skater' ); ?>">
                                    <td><?php echo esc_html( $stat->team_name ?: '(No Team)' ); ?></td>
                                    <td><?php echo esc_html( $stat->name . ' #' . $stat->number ); ?></td>
                                    <td><?php echo esc_html( $stat->position ); ?></td>
                                    <td><?php echo intval( $stat->games_played ); ?></td>
                                    <td><?php echo intval( $stat->goals ); ?></td>
                                    <td><?php echo intval( $stat->assists ); ?></td>
                                    <td><?php echo intval( $stat->penalty_minutes ); ?></td>
                                    <td><?php echo intval( $stat->shots_against ); ?></td>
                                    <td><?php echo intval( $stat->goals_allowed ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="9"><?php esc_html_e( 'No statistics available for this season.', 'bunkersnack-game-manager' ); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e( 'No seasons available. Create some games and events first.', 'bunkersnack-game-manager' ); ?></p>
            <?php endif; ?>
        </div>

        <script>
            (function() {
                document.getElementById('hge-season-select')?.addEventListener('change', function() {
                    // Simple reload - in production, you'd use AJAX
                    location.reload();
                });
            })();
        </script>
        <?php
    }

    /**
     * AJAX: Save player
     */
    public static function ajax_save_player() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $player_id = HGE_Database::save_player( $_POST );

        if ( $player_id ) {
            wp_send_json_success( array( 'id' => $player_id ) );
        } else {
            wp_send_json_error( __( 'Failed to save player', 'bunkersnack-game-manager' ) );
        }
    }

    // ===== SEASON AJAX HANDLERS =====

    /**
     * AJAX: Save season
     */
    public static function ajax_save_season() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $season_id = HGE_Database::save_season( $_POST );

        if ( $season_id ) {
            wp_send_json_success( array( 'id' => $season_id ) );
        } else {
            wp_send_json_error( __( 'Failed to save season', 'bunkersnack-game-manager' ) );
        }
    }

    /**
     * AJAX: Delete season
     */
    public static function ajax_delete_season() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $result = HGE_Database::delete_season( intval( $_POST['id'] ) );

        if ( $result ) {
            wp_send_json_success();
        } else {
            wp_send_json_error( __( 'Failed to delete season', 'bunkersnack-game-manager' ) );
        }
    }

    /**
     * AJAX: Get season
     */
    public static function ajax_get_season() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $season = HGE_Database::get_season( intval( $_POST['id'] ) );

        if ( $season ) {
            wp_send_json_success( $season );
        } else {
            wp_send_json_error( __( 'Season not found', 'bunkersnack-game-manager' ) );
        }
    }

    // ===== TEAM AJAX HANDLERS =====

    /**
     * AJAX: Save team
     */
    public static function ajax_save_team() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $team_id = HGE_Database::save_team( $_POST );

        if ( $team_id ) {
            wp_send_json_success( array( 'id' => $team_id ) );
        } else {
            wp_send_json_error( __( 'Failed to save team', 'bunkersnack-game-manager' ) );
        }
    }

    /**
     * AJAX: Delete team
     */
    public static function ajax_delete_team() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $team_id = intval( $_POST['id'] );
        HGE_Database::delete_team( $team_id );

        wp_send_json_success();
    }

    /**
     * AJAX: Get team
     */
    public static function ajax_get_team() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $team_id = intval( $_GET['id'] );
        $team = HGE_Database::get_team( $team_id );

        if ( $team ) {
            wp_send_json_success( $team );
        } else {
            wp_send_json_error( __( 'Team not found', 'bunkersnack-game-manager' ) );
        }
    }

    /**
     * AJAX: Delete player
     */
    public static function ajax_delete_player() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $player_id = intval( $_POST['id'] );
        HGE_Database::delete_player( $player_id );

        wp_send_json_success();
    }

    /**
     * AJAX: Get player
     */
    public static function ajax_get_player() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $player_id = intval( $_GET['id'] );
        $player = HGE_Database::get_player( $player_id );

        if ( $player ) {
            wp_send_json_success( $player );
        } else {
            wp_send_json_error( __( 'Player not found', 'bunkersnack-game-manager' ) );
        }
    }

    /**
     * AJAX: Save game
     */
    public static function ajax_save_game() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $game_id = HGE_Database::save_game( $_POST );

        if ( $game_id ) {
            wp_send_json_success( array( 'id' => $game_id ) );
        } else {
            wp_send_json_error( __( 'Failed to save game', 'bunkersnack-game-manager' ) );
        }
    }

    /**
     * AJAX: Delete game
     */
    public static function ajax_delete_game() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $game_id = intval( $_POST['id'] );
        HGE_Database::delete_game( $game_id );

        wp_send_json_success();
    }

    /**
     * AJAX: Get game
     */
    public static function ajax_get_game() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $game_id = intval( $_GET['id'] );
        $game = HGE_Database::get_game( $game_id );

        if ( $game ) {
            wp_send_json_success( $game );
        } else {
            wp_send_json_error( __( 'Game not found', 'bunkersnack-game-manager' ) );
        }
    }

    /**
     * AJAX: Save event
     */
    public static function ajax_save_event() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $event_id = HGE_Database::save_event( $_POST );

        if ( $event_id ) {
            // Recalculate stats for this game
            $game_id = intval( $_POST['game_id'] );
            HGE_Stats::update_game_stats( $game_id );

            wp_send_json_success( array( 'id' => $event_id ) );
        } else {
            wp_send_json_error( __( 'Failed to save event', 'bunkersnack-game-manager' ) );
        }
    }

    /**
     * AJAX: Delete event
     */
    public static function ajax_delete_event() {
        check_ajax_referer( 'hge_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $event_id = intval( $_POST['id'] );
        $event = self::get_event_by_id( $event_id );
        $game_id = $event ? intval( $event->game_id ) : 0;

        HGE_Database::delete_event( $event_id );

        // Recalculate stats
        if ( $game_id > 0 ) {
            HGE_Stats::update_game_stats( $game_id );
        }

        wp_send_json_success();
    }

    /**
     * AJAX: Get game events
     */
    public static function ajax_get_game_events() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized', 'bunkersnack-game-manager' ) );
        }

        $game_id = intval( $_GET['id'] );
        $events = HGE_Database::get_game_events( $game_id );

        if ( $events ) {
            wp_send_json_success( $events );
        } else {
            wp_send_json_success( array() );
        }
    }

    /**
     * Get event by ID
     *
     * @param int $event_id Event ID
     * @return object|null
     */
    private static function get_event_by_id( $event_id ) {
        global $wpdb;
        $events_table = $wpdb->prefix . 'hge_game_events';
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $events_table WHERE id = %d",
                $event_id
            )
        );
    }
}
