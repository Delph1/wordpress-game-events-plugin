<?php
/**
 * Shortcodes for displaying game summaries and player stats
 *
 * @package HockeyGameEvents
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hockey Game Events Shortcodes Class
 */
class HGE_Shortcodes {

    /**
     * Initialize shortcodes
     */
    public static function init() {
        add_shortcode( 'hge_game_summary', array( __CLASS__, 'game_summary_shortcode' ) );
        add_shortcode( 'hge_player_stats', array( __CLASS__, 'player_stats_shortcode' ) );
    }

    /**
     * Game Summary Shortcode
     *
     * Usage: [hge_game_summary game_id="123"]
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public static function game_summary_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'game_id' => 0,
            ),
            $atts,
            'hge_game_summary'
        );

        $game_id = intval( $atts['game_id'] );

        if ( $game_id <= 0 ) {
            return '<p>' . esc_html__( 'Invalid game ID', 'bunkersnack-game-manager' ) . '</p>';
        }

        $game = HGE_Database::get_game( $game_id );
        if ( ! $game ) {
            return '<p>' . esc_html__( 'Game not found', 'bunkersnack-game-manager' ) . '</p>';
        }

        $events = HGE_Database::get_game_events( $game_id );
        $html = '';

        // Game header
        $html .= '<div class="hge-game-summary">';
        $html .= '<h3 class="hge-game-title">';
        $html .= esc_html( date_i18n( 'Y-m-d', strtotime( $game->game_date ) ) );
        $html .= ' vs ' . esc_html( $game->opponent );
        $html .= '</h3>';

        // Score
        if ( ! is_null( $game->home_score ) && ! is_null( $game->away_score ) ) {
            $html .= '<p class="hge-game-score">';
            $html .= sprintf(
                esc_html__( 'Final Score: %d - %d', 'bunkersnack-game-manager' ),
                intval( $game->home_score ),
                intval( $game->away_score )
            );
            $html .= '</p>';
        }

        // Location
        if ( ! empty( $game->location ) ) {
            $html .= '<p class="hge-game-location">';
            $html .= '<strong>' . esc_html__( 'Location:', 'bunkersnack-game-manager' ) . '</strong> ';
            $html .= esc_html( $game->location );
            $html .= '</p>';
        }

        // Events
        if ( ! empty( $events ) ) {
            $html .= '<div class="hge-events">';
            $html .= '<h4>' . esc_html__( 'Game Events', 'bunkersnack-game-manager' ) . '</h4>';
            $html .= '<div class="hge-events-list">';

            foreach ( $events as $event ) {
                $html .= '<div class="hge-event">';
                $html .= '<div class="hge-event-header">';
                $html .= '<span class="hge-event-period">';
                $html .= sprintf(
                    esc_html__( 'P%d', 'bunkersnack-game-manager' ),
                    intval( $event->period )
                );
                $html .= '</span>';
                
                // Format event time - handle both seconds and minutes format
                $event_time_value = intval( $event->event_time );
                if ( $event_time_value > 120 ) {
                    // Assume it's in seconds (new format)
                    $minutes = intdiv( $event_time_value, 60 );
                    $seconds = $event_time_value % 60;
                    $time_display = $minutes . ':' . str_pad( $seconds, 2, '0', STR_PAD_LEFT );
                } else {
                    // Assume it's in minutes (old format)
                    $time_display = $event_time_value . ':00';
                }
                
                $html .= '<span class="hge-event-time">' . esc_html( $time_display ) . '</span>';
                $html .= '</div>';

                $html .= '<div class="hge-event-body">';
                $html .= '<span class="hge-event-type">' . esc_html( ucfirst( $event->event_type ) ) . '</span>';
                if ( $event->name ) {
                    $html .= '<span class="hge-event-player"> - ' . esc_html( $event->name );
                    if ( $event->number ) {
                        $html .= ' #' . esc_html( $event->number );
                    }
                    $html .= '</span>';
                }
                if ( ! empty( $event->description ) ) {
                    $html .= '<p class="hge-event-description">' . wp_kses_post( $event->description ) . '</p>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
            $html .= '</div>';
        }

        // Notes
        if ( ! empty( $game->notes ) ) {
            $html .= '<div class="hge-game-notes">';
            $html .= '<h4>' . esc_html__( 'Notes', 'bunkersnack-game-manager' ) . '</h4>';
            $html .= wp_kses_post( $game->notes );
            $html .= '</div>';
        }

        $html .= '</div>';

        // Add styles
        wp_enqueue_style( 'hge-frontend', HGE_PLUGIN_URL . 'assets/css/frontend.css' );

        return $html;
    }

    /**
     * Player Stats Shortcode
     *
     * Usage: [hge_player_stats season="Elitserien 76/77" sortby="goals"]
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public static function player_stats_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'season'  => null,
                'sortby'  => 'goals',
                'sortdir' => 'DESC',
            ),
            $atts,
            'hge_player_stats'
        );

        // If no season specified, try to get the most recent one
        if ( is_null( $atts['season'] ) || empty( $atts['season'] ) ) {
            $seasons = HGE_Database::get_all_seasons();
            if ( empty( $seasons ) ) {
                return '<p>' . esc_html__( 'No seasons available.', 'bunkersnack-game-manager' ) . '</p>';
            }
            $season = $seasons[0]; // Most recent season
        } else {
            $season = sanitize_text_field( $atts['season'] );
        }

        $sortby = sanitize_text_field( $atts['sortby'] );
        $sortdir = strtoupper( sanitize_text_field( $atts['sortdir'] ) );

        // Validate sortby
        $allowed_sorts = array( 'goals', 'assists', 'games_played', 'penalty_minutes', 'shots_against' );
        if ( ! in_array( $sortby, $allowed_sorts, true ) ) {
            $sortby = 'goals';
        }

        // Validate sort direction
        if ( ! in_array( $sortdir, array( 'ASC', 'DESC' ), true ) ) {
            $sortdir = 'DESC';
        }

        $stats = HGE_Stats::get_season_stats(
            $season,
            array(
                'orderby' => $sortby,
                'order'   => $sortdir,
            )
        );

        if ( empty( $stats ) ) {
            return '<p>' . esc_html__( 'No player statistics available for this season.', 'bunkersnack-game-manager' ) . '</p>';
        }

        $html = '';

        $html .= '<div class="hge-player-stats">';
        $html .= '<h3 class="hge-stats-title">';
        $html .= sprintf( esc_html__( 'Player Statistics - %s', 'bunkersnack-game-manager' ), esc_html( $season ) );
        $html .= '</h3>';

        $html .= '<table class="hge-stats-table" data-sortable="true">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="hge-col-team">' . esc_html__( 'Team', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '<th class="hge-col-name">' . esc_html__( 'Player', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '<th class="hge-col-number">#</th>';
        $html .= '<th class="hge-col-position">' . esc_html__( 'Position', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '<th class="hge-col-gp">' . esc_html__( 'GP', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '<th class="hge-col-g">' . esc_html__( 'G', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '<th class="hge-col-a">' . esc_html__( 'A', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '<th class="hge-col-pim">' . esc_html__( 'PIM', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '<th class="hge-col-sa">' . esc_html__( 'SA', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '<th class="hge-col-ga">' . esc_html__( 'GA', 'bunkersnack-game-manager' ) . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ( $stats as $stat ) {
            $html .= '<tr class="' . ( $stat->is_goalie ? 'hge-goalie' : 'hge-skater' ) . '">';
            $html .= '<td class="hge-col-team">' . esc_html( $stat->team_name ) . '</td>';
            $html .= '<td class="hge-col-name">' . esc_html( $stat->name ) . '</td>';
            $html .= '<td class="hge-col-number">' . esc_html( $stat->number ) . '</td>';
            $html .= '<td class="hge-col-position">' . esc_html( $stat->position ) . '</td>';
            $html .= '<td class="hge-col-gp">' . intval( $stat->games_played ) . '</td>';
            $html .= '<td class="hge-col-g">' . intval( $stat->goals ) . '</td>';
            $html .= '<td class="hge-col-a">' . intval( $stat->assists ) . '</td>';
            $html .= '<td class="hge-col-pim">' . intval( $stat->penalty_minutes ) . '</td>';
            $html .= '<td class="hge-col-sa">' . intval( $stat->shots_against ) . '</td>';
            $html .= '<td class="hge-col-ga">' . intval( $stat->goals_allowed ) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        // Add styles
        wp_enqueue_style( 'hge-frontend', HGE_PLUGIN_URL . 'assets/css/frontend.css' );

        return $html;
    }
}
