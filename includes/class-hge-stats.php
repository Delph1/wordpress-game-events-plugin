<?php
/**
 * Player Statistics Management
 *
 * @package HockeyGameEvents
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hockey Game Events Stats Class
 */
class HGE_Stats {

    /**
     * Initialize stats class
     */
    public static function init() {
        // Stats initialization
    }

    /**
     * Calculate and update player stats for a game
     *
     * @param int $game_id Game ID
     */
    public static function update_game_stats( $game_id ) {
        $game = HGE_Database::get_game( $game_id );
        if ( ! $game ) {
            return;
        }

        $events = HGE_Database::get_game_events( $game_id );
        $players_stats = array();

        // Use game's season or fall back to year from game date
        $season = ! empty( $game->season ) ? $game->season : date( 'Y', strtotime( $game->game_date ) );

        // Process events
        foreach ( $events as $event ) {
            if ( ! $event->player_id ) {
                continue;
            }

            if ( ! isset( $players_stats[ $event->player_id ] ) ) {
                $players_stats[ $event->player_id ] = array(
                    'player_id'      => $event->player_id,
                    'season'         => $season,
                    'games_played'   => 0,
                    'goals'          => 0,
                    'assists'        => 0,
                    'penalty_minutes' => 0,
                    'goals_allowed'  => 0,
                    'shots_against'  => 0,
                );
            }

            switch ( $event->event_type ) {
                case 'goal':
                    $players_stats[ $event->player_id ]['goals']++;
                    break;
                case 'assist':
                    $players_stats[ $event->player_id ]['assists']++;
                    break;
                case 'penalty':
                    // Extract penalty minutes from description if possible
                    $penalty_minutes = self::extract_penalty_minutes( $event->description );
                    $players_stats[ $event->player_id ]['penalty_minutes'] += $penalty_minutes;
                    break;
                case 'goal_allowed':
                    $players_stats[ $event->player_id ]['goals_allowed']++;
                    break;
                case 'shot_against':
                    $players_stats[ $event->player_id ]['shots_against']++;
                    break;
            }
        }

        // Get all players who participated
        $participating_players = array_keys( $players_stats );

        // Mark games played for participating players
        foreach ( $participating_players as $player_id ) {
            $players_stats[ $player_id ]['games_played'] = self::count_player_games( $player_id, $season );
        }

        // Update stats in database
        foreach ( $players_stats as $stats ) {
            self::save_player_stats( $stats );
        }
    }

    /**
     * Extract penalty minutes from description
     *
     * @param string $description Event description
     * @return int Penalty minutes
     */
    private static function extract_penalty_minutes( $description ) {
        // Look for common patterns like "2 min", "5 minutes", etc.
        if ( preg_match( '/(\d+)\s*(?:min|minute)/', $description, $matches ) ) {
            return intval( $matches[1] );
        }
        return 2; // Default to 2 minutes if not specified
    }

    /**
     * Count how many games a player has appeared in
     *
     * @param int $player_id Player ID
     * @param string $season Season name or year
     * @return int Number of games
     */
    private static function count_player_games( $player_id, $season ) {
        global $wpdb;
        $events_table = $wpdb->prefix . 'hge_game_events';
        $games_table = $wpdb->prefix . 'hge_games';

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT g.id) FROM $events_table e
                INNER JOIN $games_table g ON e.game_id = g.id
                WHERE e.player_id = %d AND g.season = %s",
                $player_id,
                $season
            )
        );
    }

    /**
     * Save or update player stats
     *
     * @param array $stats Stats array
     * @return bool
     */
    private static function save_player_stats( $stats ) {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'hge_player_stats';

        // Check if stats exist
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM $stats_table WHERE player_id = %d AND season = %d",
                $stats['player_id'],
                $stats['season']
            )
        );

        if ( $existing ) {
            // Update
            return $wpdb->update(
                $stats_table,
                array(
                    'games_played'    => $stats['games_played'],
                    'goals'           => $stats['goals'],
                    'assists'         => $stats['assists'],
                    'penalty_minutes' => $stats['penalty_minutes'],
                    'goals_allowed'   => $stats['goals_allowed'],
                    'shots_against'   => $stats['shots_against'],
                ),
                array( 'player_id' => $stats['player_id'], 'season' => $stats['season'] ),
                array( '%d', '%d', '%d', '%d', '%d', '%d' ),
                array( '%d', '%d' )
            );
        } else {
            // Insert
            return $wpdb->insert(
                $stats_table,
                array(
                    'player_id'      => $stats['player_id'],
                    'season'         => $stats['season'],
                    'games_played'   => $stats['games_played'],
                    'goals'          => $stats['goals'],
                    'assists'        => $stats['assists'],
                    'penalty_minutes' => $stats['penalty_minutes'],
                    'goals_allowed'  => $stats['goals_allowed'],
                    'shots_against'  => $stats['shots_against'],
                ),
                array( '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d' )
            );
        }
    }

    /**
     * Get player stats
     *
     * @param int $player_id Player ID
     * @param string $season Season name
     * @return object|null
     */
    public static function get_player_stats( $player_id, $season = null ) {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'hge_player_stats';

        if ( is_null( $season ) ) {
            $season = date( 'Y' );
        }

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $stats_table WHERE player_id = %d AND season = %s",
                $player_id,
                $season
            )
        );
    }

    /**
     * Get all player stats for a season
     *
     * @param string $season Season name
     * @param array $args Query arguments
     * @return array
     */
    public static function get_season_stats( $season = null, $args = array() ) {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'hge_player_stats';
        $players_table = $wpdb->prefix . 'hge_players';
        $teams_table = $wpdb->prefix . 'hge_teams';

        if ( is_null( $season ) ) {
            $season = date( 'Y' );
        }

        $orderby = ! empty( $args['orderby'] ) ? $args['orderby'] : 'goals';
        $order = ! empty( $args['order'] ) ? strtoupper( $args['order'] ) : 'DESC';

        $query = $wpdb->prepare(
            "SELECT s.*, p.name, p.position, p.number, p.is_goalie, t.name as team_name FROM $stats_table s
            INNER JOIN $players_table p ON s.player_id = p.id
            LEFT JOIN $teams_table t ON p.team_id = t.id
            WHERE s.season = %s
            ORDER BY t.name ASC, s.$orderby $order",
            $season
        );

        return $wpdb->get_results( $query );
    }
}
