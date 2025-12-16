<?php
/**
 * Database Management
 *
 * @package HockeyGameEvents
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hockey Game Events Database Class
 */
class HGE_Database {

    /**
     * Initialize database class
     */
    public static function init() {
        // Database initialization on plugin load
    }

    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Seasons table
        $seasons_table = $wpdb->prefix . 'hge_seasons';
        $seasons_sql = "CREATE TABLE IF NOT EXISTS $seasons_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            description LONGTEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY name_idx (name)
        ) $charset_collate;";

        // Teams table
        $teams_table = $wpdb->prefix . 'hge_teams';
        $teams_sql = "CREATE TABLE IF NOT EXISTS $teams_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            shortcode VARCHAR(50),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY name_idx (name),
            UNIQUE KEY shortcode_idx (shortcode)
        ) $charset_collate;";

        // Players table
        $players_table = $wpdb->prefix . 'hge_players';
        $players_sql = "CREATE TABLE IF NOT EXISTS $players_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            team_id BIGINT(20) UNSIGNED,
            name VARCHAR(100) NOT NULL,
            number INT(3),
            position VARCHAR(50),
            is_goalie TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY name_idx (name),
            KEY team_id_idx (team_id),
            FOREIGN KEY (team_id) REFERENCES $teams_table(id) ON DELETE SET NULL
        ) $charset_collate;";

        // Games table
        $games_table = $wpdb->prefix . 'hge_games';
        $games_sql = "CREATE TABLE IF NOT EXISTS $games_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            season VARCHAR(50),
            game_date DATE NOT NULL,
            home_team_id BIGINT(20) UNSIGNED,
            away_team_id BIGINT(20) UNSIGNED,
            opponent VARCHAR(100),
            home_score INT(3),
            away_score INT(3),
            location VARCHAR(255),
            attendance INT(5),
            home_shots_p1 INT(3),
            home_shots_p2 INT(3),
            home_shots_p3 INT(3),
            home_shots_ot INT(3),
            home_shots_ps INT(3),
            away_shots_p1 INT(3),
            away_shots_p2 INT(3),
            away_shots_p3 INT(3),
            away_shots_ot INT(3),
            away_shots_ps INT(3),
            home_goals_p1 INT(3),
            home_goals_p2 INT(3),
            home_goals_p3 INT(3),
            home_goals_ot INT(3),
            home_goals_ps INT(3),
            away_goals_p1 INT(3),
            away_goals_p2 INT(3),
            away_goals_p3 INT(3),
            away_goals_ot INT(3),
            away_goals_ps INT(3),
            notes LONGTEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY game_date_idx (game_date),
            KEY season_idx (season),
            KEY home_team_id_idx (home_team_id),
            KEY away_team_id_idx (away_team_id),
            FOREIGN KEY (home_team_id) REFERENCES $teams_table(id) ON DELETE SET NULL,
            FOREIGN KEY (away_team_id) REFERENCES $teams_table(id) ON DELETE SET NULL
        ) $charset_collate;";

        // Game Events table
        $events_table = $wpdb->prefix . 'hge_game_events';
        $events_sql = "CREATE TABLE IF NOT EXISTS $events_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            game_id BIGINT(20) UNSIGNED NOT NULL,
            player_id BIGINT(20) UNSIGNED,
            event_type VARCHAR(50) NOT NULL,
            event_time INT(3),
            period INT(2),
            parent_event_id BIGINT(20) UNSIGNED,
            description LONGTEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY game_id_idx (game_id),
            KEY player_id_idx (player_id),
            KEY parent_event_id_idx (parent_event_id),
            FOREIGN KEY (game_id) REFERENCES $games_table(id) ON DELETE CASCADE,
            FOREIGN KEY (player_id) REFERENCES $players_table(id) ON DELETE SET NULL,
            FOREIGN KEY (parent_event_id) REFERENCES $events_table(id) ON DELETE CASCADE
        ) $charset_collate;";

        // Player Stats Cache table
        $stats_table = $wpdb->prefix . 'hge_player_stats';
        $stats_sql = "CREATE TABLE IF NOT EXISTS $stats_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            player_id BIGINT(20) UNSIGNED NOT NULL,
            season YEAR NOT NULL,
            games_played INT(3) DEFAULT 0,
            goals INT(3) DEFAULT 0,
            assists INT(3) DEFAULT 0,
            penalty_minutes INT(3) DEFAULT 0,
            goals_allowed INT(3) DEFAULT 0,
            shots_against INT(3) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY player_season_idx (player_id, season),
            FOREIGN KEY (player_id) REFERENCES $players_table(id) ON DELETE CASCADE
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $seasons_sql );
        dbDelta( $teams_sql );
        dbDelta( $players_sql );
        dbDelta( $games_sql );
        dbDelta( $events_sql );
        dbDelta( $stats_sql );

        // Add team_id column to players table if it doesn't exist (for existing installations)
        $columns = $wpdb->get_results( "SHOW COLUMNS FROM $players_table" );
        $column_names = wp_list_pluck( $columns, 'Field' );
        
        if ( ! in_array( 'team_id', $column_names, true ) ) {
            $wpdb->query( "ALTER TABLE $players_table ADD COLUMN team_id BIGINT(20) UNSIGNED AFTER id" );
            $wpdb->query( "ALTER TABLE $players_table ADD KEY team_id_idx (team_id)" );
            $wpdb->query( "ALTER TABLE $players_table ADD CONSTRAINT fk_player_team FOREIGN KEY (team_id) REFERENCES $teams_table(id) ON DELETE SET NULL" );
        }

        // Add home_team_id and away_team_id columns to games table if they don't exist
        $game_columns = $wpdb->get_results( "SHOW COLUMNS FROM $games_table" );
        $game_column_names = wp_list_pluck( $game_columns, 'Field' );
        
        if ( ! in_array( 'home_team_id', $game_column_names, true ) ) {
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_team_id BIGINT(20) UNSIGNED AFTER season" );
            $wpdb->query( "ALTER TABLE $games_table ADD KEY home_team_id_idx (home_team_id)" );
            $wpdb->query( "ALTER TABLE $games_table ADD CONSTRAINT fk_game_home_team FOREIGN KEY (home_team_id) REFERENCES $teams_table(id) ON DELETE SET NULL" );
        }
        
        if ( ! in_array( 'away_team_id', $game_column_names, true ) ) {
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_team_id BIGINT(20) UNSIGNED AFTER home_team_id" );
            $wpdb->query( "ALTER TABLE $games_table ADD KEY away_team_id_idx (away_team_id)" );
            $wpdb->query( "ALTER TABLE $games_table ADD CONSTRAINT fk_game_away_team FOREIGN KEY (away_team_id) REFERENCES $teams_table(id) ON DELETE SET NULL" );
        }

        // Add parent_event_id column to game_events table if it doesn't exist (for assist tracking)
        $event_columns = $wpdb->get_results( "SHOW COLUMNS FROM $events_table" );
        $event_column_names = wp_list_pluck( $event_columns, 'Field' );
        
        if ( ! in_array( 'parent_event_id', $event_column_names, true ) ) {
            $wpdb->query( "ALTER TABLE $events_table ADD COLUMN parent_event_id BIGINT(20) UNSIGNED AFTER game_id" );
            $wpdb->query( "ALTER TABLE $events_table ADD KEY parent_event_id_idx (parent_event_id)" );
            $wpdb->query( "ALTER TABLE $events_table ADD CONSTRAINT fk_event_parent_event FOREIGN KEY (parent_event_id) REFERENCES $events_table(id) ON DELETE CASCADE" );
        }

        // Add game statistics columns (shots and goals per period, attendance) if they don't exist
        if ( ! in_array( 'attendance', $game_column_names, true ) ) {
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN attendance INT(5) AFTER location" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_shots_p1 INT(3) AFTER attendance" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_shots_p2 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_shots_p3 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_shots_ot INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_shots_ps INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_shots_p1 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_shots_p2 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_shots_p3 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_shots_ot INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_shots_ps INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_goals_p1 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_goals_p2 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_goals_p3 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_goals_ot INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN home_goals_ps INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_goals_p1 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_goals_p2 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_goals_p3 INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_goals_ot INT(3)" );
            $wpdb->query( "ALTER TABLE $games_table ADD COLUMN away_goals_ps INT(3)" );
        }
    }

    // ===== SEASONS METHODS =====

    /**
     * Get all seasons
     *
     * @return array
     */
    public static function get_all_seasons_list() {
        global $wpdb;
        $seasons_table = $wpdb->prefix . 'hge_seasons';
        return $wpdb->get_results( "SELECT * FROM $seasons_table ORDER BY name DESC" );
    }

    /**
     * Get a season by ID
     *
     * @param int $season_id Season ID
     * @return object|null
     */
    public static function get_season( $season_id ) {
        global $wpdb;
        $seasons_table = $wpdb->prefix . 'hge_seasons';
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $seasons_table WHERE id = %d",
                $season_id
            )
        );
    }

    /**
     * Create or update a season
     *
     * @param array $data Season data
     * @return int|false Season ID or false
     */
    public static function save_season( $data ) {
        global $wpdb;
        $seasons_table = $wpdb->prefix . 'hge_seasons';

        if ( isset( $data['id'] ) && $data['id'] > 0 ) {
            // Update
            return $wpdb->update(
                $seasons_table,
                array(
                    'name'        => sanitize_text_field( $data['name'] ),
                    'description' => wp_kses_post( $data['description'] ?? '' ),
                ),
                array( 'id' => $data['id'] ),
                array( '%s', '%s' ),
                array( '%d' )
            );
        } else {
            // Insert
            $wpdb->insert(
                $seasons_table,
                array(
                    'name'        => sanitize_text_field( $data['name'] ),
                    'description' => wp_kses_post( $data['description'] ?? '' ),
                ),
                array( '%s', '%s' )
            );
            return $wpdb->insert_id;
        }
    }

    /**
     * Delete a season
     *
     * @param int $season_id Season ID
     * @return int|false
     */
    public static function delete_season( $season_id ) {
        global $wpdb;
        $seasons_table = $wpdb->prefix . 'hge_seasons';
        return $wpdb->delete(
            $seasons_table,
            array( 'id' => $season_id ),
            array( '%d' )
        );
    }

    // ===== TEAMS METHODS =====

    /**
     * Get all teams
     *
     * @return array
     */
    public static function get_all_teams() {
        global $wpdb;
        $teams_table = $wpdb->prefix . 'hge_teams';
        return $wpdb->get_results( "SELECT * FROM $teams_table ORDER BY name ASC" );
    }

    /**
     * Get a team by ID
     *
     * @param int $team_id Team ID
     * @return object|null
     */
    public static function get_team( $team_id ) {
        global $wpdb;
        $teams_table = $wpdb->prefix . 'hge_teams';
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $teams_table WHERE id = %d",
                $team_id
            )
        );
    }

    /**
     * Create or update a team
     *
     * @param array $data Team data
     * @return int|false Team ID or false
     */
    public static function save_team( $data ) {
        global $wpdb;
        $teams_table = $wpdb->prefix . 'hge_teams';

        if ( isset( $data['id'] ) && $data['id'] > 0 ) {
            // Update
            return $wpdb->update(
                $teams_table,
                array(
                    'name'      => sanitize_text_field( $data['name'] ),
                    'shortcode' => sanitize_text_field( $data['shortcode'] ?? '' ),
                ),
                array( 'id' => $data['id'] ),
                array( '%s', '%s' ),
                array( '%d' )
            );
        } else {
            // Insert
            $wpdb->insert(
                $teams_table,
                array(
                    'name'      => sanitize_text_field( $data['name'] ),
                    'shortcode' => sanitize_text_field( $data['shortcode'] ?? '' ),
                ),
                array( '%s', '%s' )
            );
            return $wpdb->insert_id;
        }
    }

    /**
     * Delete a team
     *
     * @param int $team_id Team ID
     * @return int|false
     */
    public static function delete_team( $team_id ) {
        global $wpdb;
        $teams_table = $wpdb->prefix . 'hge_teams';
        return $wpdb->delete(
            $teams_table,
            array( 'id' => $team_id ),
            array( '%d' )
        );
    }

    // ===== PLAYERS METHODS =====

    /**
     * Get a single player
     *
     * @param int $player_id Player ID
     * @return object|null
     */
    public static function get_player( $player_id ) {
        global $wpdb;
        $players_table = $wpdb->prefix . 'hge_players';
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $players_table WHERE id = %d",
                $player_id
            )
        );
    }

    /**
     * Get all players
     *
     * @return array
     */
    public static function get_all_players() {
        global $wpdb;
        $players_table = $wpdb->prefix . 'hge_players';
        $teams_table = $wpdb->prefix . 'hge_teams';
        return $wpdb->get_results( 
            "SELECT p.*, t.name as team_name FROM $players_table p
            LEFT JOIN $teams_table t ON p.team_id = t.id
            ORDER BY t.name ASC, p.name ASC" 
        );
    }

    /**
     * Create or update a player
     *
     * @param array $data Player data
     * @return int|false Player ID or false
     */
    public static function save_player( $data ) {
        global $wpdb;
        $players_table = $wpdb->prefix . 'hge_players';

        if ( isset( $data['id'] ) && $data['id'] > 0 ) {
            // Update
            return $wpdb->update(
                $players_table,
                array(
                    'team_id'    => ! empty( $data['team_id'] ) ? intval( $data['team_id'] ) : null,
                    'name'       => sanitize_text_field( $data['name'] ),
                    'number'     => ! empty( $data['number'] ) ? intval( $data['number'] ) : null,
                    'position'   => sanitize_text_field( $data['position'] ?? '' ),
                    'is_goalie'  => isset( $data['is_goalie'] ) ? 1 : 0,
                ),
                array( 'id' => $data['id'] ),
                array( '%d', '%s', '%d', '%s', '%d' ),
                array( '%d' )
            );
        } else {
            // Insert
            $wpdb->insert(
                $players_table,
                array(
                    'team_id'    => ! empty( $data['team_id'] ) ? intval( $data['team_id'] ) : null,
                    'name'       => sanitize_text_field( $data['name'] ),
                    'number'     => ! empty( $data['number'] ) ? intval( $data['number'] ) : null,
                    'position'   => sanitize_text_field( $data['position'] ?? '' ),
                    'is_goalie'  => isset( $data['is_goalie'] ) ? 1 : 0,
                ),
                array( '%d', '%s', '%d', '%s', '%d' )
            );
            return $wpdb->insert_id;
        }
    }

    /**
     * Delete a player
     *
     * @param int $player_id Player ID
     * @return int|false
     */
    public static function delete_player( $player_id ) {
        global $wpdb;
        $players_table = $wpdb->prefix . 'hge_players';
        return $wpdb->delete(
            $players_table,
            array( 'id' => $player_id ),
            array( '%d' )
        );
    }

    /**
     * Get a game by ID
     *
     * @param int $game_id Game ID
     * @return object|null
     */
    public static function get_game( $game_id ) {
        global $wpdb;
        $games_table = $wpdb->prefix . 'hge_games';
        $teams_table = $wpdb->prefix . 'hge_teams';
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT g.*, ht.name as home_team_name, at.name as away_team_name 
                FROM $games_table g
                LEFT JOIN $teams_table ht ON g.home_team_id = ht.id
                LEFT JOIN $teams_table at ON g.away_team_id = at.id
                WHERE g.id = %d",
                $game_id
            )
        );
    }

    /**
     * Get all games
     *
     * @param array $args Query arguments
     * @return array
     */
    public static function get_all_games( $args = array() ) {
        global $wpdb;
        $games_table = $wpdb->prefix . 'hge_games';
        $teams_table = $wpdb->prefix . 'hge_teams';
        
        $limit = ! empty( $args['limit'] ) ? intval( $args['limit'] ) : -1;
        $offset = ! empty( $args['offset'] ) ? intval( $args['offset'] ) : 0;
        $orderby = ! empty( $args['orderby'] ) ? $args['orderby'] : 'game_date';
        $order = ! empty( $args['order'] ) ? strtoupper( $args['order'] ) : 'DESC';
        $season = ! empty( $args['season'] ) ? sanitize_text_field( $args['season'] ) : '';

        $query = "SELECT g.*, 
                         ht.name as home_team_name, 
                         at.name as away_team_name 
                  FROM $games_table g
                  LEFT JOIN $teams_table ht ON g.home_team_id = ht.id
                  LEFT JOIN $teams_table at ON g.away_team_id = at.id";
        
        if ( ! empty( $season ) ) {
            $query .= $wpdb->prepare( " WHERE g.season = %s", $season );
        }
        
        $query .= " ORDER BY g.$orderby $order";
        
        if ( $limit > 0 ) {
            $query .= $wpdb->prepare( " LIMIT %d OFFSET %d", $limit, $offset );
        }

        return $wpdb->get_results( $query );
    }

    /**
     * Get all seasons
     *
     * @return array
     */
    public static function get_all_seasons() {
        global $wpdb;
        $games_table = $wpdb->prefix . 'hge_games';
        
        return $wpdb->get_col( "SELECT DISTINCT season FROM $games_table WHERE season IS NOT NULL AND season != '' ORDER BY season DESC" );
    }

    /**
     * Create or update a game
     *
     * @param array $data Game data
     * @return int|false Game ID or false
     */
    public static function save_game( $data ) {
        global $wpdb;
        $games_table = $wpdb->prefix . 'hge_games';

        // Prepare base game data
        $game_data = array(
            'season'       => sanitize_text_field( $data['season'] ?? '' ),
            'game_date'    => sanitize_text_field( $data['game_date'] ),
            'home_team_id' => ! empty( $data['home_team'] ) ? intval( $data['home_team'] ) : null,
            'away_team_id' => ! empty( $data['away_team'] ) ? intval( $data['away_team'] ) : null,
            'opponent'     => sanitize_text_field( $data['opponent'] ?? '' ),
            'home_score'   => isset( $data['home_score'] ) && $data['home_score'] !== '' ? intval( $data['home_score'] ) : null,
            'away_score'   => isset( $data['away_score'] ) && $data['away_score'] !== '' ? intval( $data['away_score'] ) : null,
            'location'     => sanitize_text_field( $data['location'] ?? '' ),
            'attendance'   => ! empty( $data['attendance'] ) ? intval( $data['attendance'] ) : null,
            'notes'        => wp_kses_post( $data['notes'] ?? '' ),
        );

        // Add shot statistics
        $shot_periods = array( 'p1', 'p2', 'p3', 'ot', 'ps' );
        foreach ( $shot_periods as $period ) {
            $game_data[ "home_shots_$period" ] = ! empty( $data[ "home_shots_$period" ] ) ? intval( $data[ "home_shots_$period" ] ) : null;
            $game_data[ "away_shots_$period" ] = ! empty( $data[ "away_shots_$period" ] ) ? intval( $data[ "away_shots_$period" ] ) : null;
        }

        // Add goal statistics
        foreach ( $shot_periods as $period ) {
            $game_data[ "home_goals_$period" ] = ! empty( $data[ "home_goals_$period" ] ) ? intval( $data[ "home_goals_$period" ] ) : null;
            $game_data[ "away_goals_$period" ] = ! empty( $data[ "away_goals_$period" ] ) ? intval( $data[ "away_goals_$period" ] ) : null;
        }

        // Build format specifiers array
        $format_specifiers = array();
        foreach ( $game_data as $key => $value ) {
            // String fields
            if ( in_array( $key, array( 'season', 'game_date', 'opponent', 'location', 'notes' ) ) ) {
                $format_specifiers[] = '%s';
            } else {
                // Numeric fields
                $format_specifiers[] = '%d';
            }
        }

        if ( isset( $data['id'] ) && $data['id'] > 0 ) {
            // Update
            return $wpdb->update(
                $games_table,
                $game_data,
                array( 'id' => $data['id'] ),
                $format_specifiers,
                array( '%d' )
            );
        } else {
            // Insert
            $wpdb->insert(
                $games_table,
                $game_data,
                $format_specifiers
            );
            return $wpdb->insert_id;
        }
    }

    /**
     * Delete a game
     *
     * @param int $game_id Game ID
     * @return int|false
     */
    public static function delete_game( $game_id ) {
        global $wpdb;
        $games_table = $wpdb->prefix . 'hge_games';
        return $wpdb->delete(
            $games_table,
            array( 'id' => $game_id ),
            array( '%d' )
        );
    }

    /**
     * Get game events
     *
     * @param int $game_id Game ID
     * @return array
     */
    public static function get_game_events( $game_id ) {
        global $wpdb;
        $events_table = $wpdb->prefix . 'hge_game_events';
        $players_table = $wpdb->prefix . 'hge_players';
        $teams_table = $wpdb->prefix . 'hge_teams';
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT e.*, p.name, p.number, p.team_id, p.is_goalie, t.name as team_name, t.shortcode as team_shortcode FROM $events_table e
                LEFT JOIN $players_table p ON e.player_id = p.id
                LEFT JOIN $teams_table t ON p.team_id = t.id
                WHERE e.game_id = %d
                ORDER BY e.period ASC, e.event_time ASC",
                $game_id
            )
        );
    }

    /**
     * Create a game event
     *
     * @param array $data Event data
     * @return int|false Event ID or false
     */
    public static function save_event( $data ) {
        global $wpdb;
        $events_table = $wpdb->prefix . 'hge_game_events';

        if ( isset( $data['id'] ) && $data['id'] > 0 ) {
            // Update
            $result = $wpdb->update(
                $events_table,
                array(
                    'game_id'      => intval( $data['game_id'] ),
                    'player_id'    => ! empty( $data['player_id'] ) ? intval( $data['player_id'] ) : null,
                    'event_type'   => sanitize_text_field( $data['event_type'] ),
                    'event_time'   => ! empty( $data['event_time'] ) ? intval( $data['event_time'] ) : 0,
                    'period'       => ! empty( $data['period'] ) ? intval( $data['period'] ) : 1,
                    'description'  => wp_kses_post( $data['description'] ?? '' ),
                ),
                array( 'id' => $data['id'] ),
                array( '%d', '%d', '%s', '%d', '%d', '%s' ),
                array( '%d' )
            );

            // Handle assists for goals
            if ( $result && 'goal' === sanitize_text_field( $data['event_type'] ) ) {
                self::save_goal_assists( intval( $data['id'] ), $data['assists'] ?? array() );
            }

            return $result;
        } else {
            // Insert
            $wpdb->insert(
                $events_table,
                array(
                    'game_id'      => intval( $data['game_id'] ),
                    'player_id'    => ! empty( $data['player_id'] ) ? intval( $data['player_id'] ) : null,
                    'event_type'   => sanitize_text_field( $data['event_type'] ),
                    'event_time'   => ! empty( $data['event_time'] ) ? intval( $data['event_time'] ) : 0,
                    'period'       => ! empty( $data['period'] ) ? intval( $data['period'] ) : 1,
                    'description'  => wp_kses_post( $data['description'] ?? '' ),
                ),
                array( '%d', '%d', '%s', '%d', '%d', '%s' )
            );

            $event_id = $wpdb->insert_id;

            // Handle assists for goals
            if ( $event_id && 'goal' === sanitize_text_field( $data['event_type'] ) ) {
                self::save_goal_assists( $event_id, $data['assists'] ?? array() );
            }

            return $event_id;
        }
    }

    /**
     * Save assists for a goal
     *
     * @param int   $event_id Event ID (the goal)
     * @param array $assists  Array of player IDs who assisted
     */
    public static function save_goal_assists( $event_id, $assists = array() ) {
        global $wpdb;
        $events_table = $wpdb->prefix . 'hge_game_events';

        // Delete existing assists for this goal
        $wpdb->delete(
            $events_table,
            array(
                'parent_event_id' => intval( $event_id ),
                'event_type'      => 'assist',
            ),
            array( '%d', '%s' )
        );

        // Insert new assists
        if ( is_array( $assists ) && ! empty( $assists ) ) {
            foreach ( $assists as $assist_player_id ) {
                $assist_player_id = intval( $assist_player_id );
                if ( $assist_player_id > 0 ) {
                    // Get the goal event details
                    $goal_event = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT game_id, period, event_time FROM $events_table WHERE id = %d",
                            intval( $event_id )
                        )
                    );

                    if ( $goal_event ) {
                        $wpdb->insert(
                            $events_table,
                            array(
                                'game_id'          => intval( $goal_event->game_id ),
                                'player_id'        => $assist_player_id,
                                'event_type'       => 'assist',
                                'event_time'       => intval( $goal_event->event_time ),
                                'period'           => intval( $goal_event->period ),
                                'parent_event_id'  => intval( $event_id ),
                                'description'      => '',
                            ),
                            array( '%d', '%d', '%s', '%d', '%d', '%d', '%s' )
                        );
                    }
                }
            }
        }
    }

    /**
     * Delete a game event
     *
     * @param int $event_id Event ID
     * @return int|false
     */
    public static function delete_event( $event_id ) {
        global $wpdb;
        $events_table = $wpdb->prefix . 'hge_game_events';
        return $wpdb->delete(
            $events_table,
            array( 'id' => $event_id ),
            array( '%d' )
        );
    }
}
