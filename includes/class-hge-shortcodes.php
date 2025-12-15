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
        $html .= esc_html( date_i18n( 'Y-m-d', strtotime( $game->game_date ) ) ) . ' - ';
        
        // Show team names if available
        if ( ! empty( $game->home_team_name ) ) {
            $html .= esc_html( $game->home_team_name );
        } else {
            $html .= esc_html__( 'Home Team', 'bunkersnack-game-manager' );
        }
        
        $html .= ' vs ';
        
        if ( ! empty( $game->away_team_name ) ) {
            $html .= esc_html( $game->away_team_name );
        } elseif ( ! empty( $game->opponent ) ) {
            $html .= esc_html( $game->opponent );
        } else {
            $html .= esc_html__( 'Away Team', 'bunkersnack-game-manager' );
        }
        
        $html .= '</h3>';

        // Score and Goals
        $html .= '<p class="hge-game-score">';
        if ( ! is_null( $game->home_score ) && ! is_null( $game->away_score ) ) {
            $html .= sprintf(
                esc_html__( 'Final Score: %d - %d', 'bunkersnack-game-manager' ),
                intval( $game->home_score ),
                intval( $game->away_score )
            );
        }

        // Add period goals if available
        $has_goals = ( ! is_null( $game->home_goals_p1 ) || ! is_null( $game->home_goals_p2 ) || ! is_null( $game->home_goals_p3 ) ||
                     ! is_null( $game->away_goals_p1 ) || ! is_null( $game->away_goals_p2 ) || ! is_null( $game->away_goals_p3 ) );
        
        if ( $has_goals ) {
            $html .= ' (' . intval( $game->home_goals_p1 ?? 0 ) . '-' . intval( $game->away_goals_p1 ?? 0 ) . ', ';
            $html .= intval( $game->home_goals_p2 ?? 0 ) . '-' . intval( $game->away_goals_p2 ?? 0 ) . ', ';
            $html .= intval( $game->home_goals_p3 ?? 0 ) . '-' . intval( $game->away_goals_p3 ?? 0 ) . ')';
        }

        $html .= '</p>';

        // Shots Statistics
        $has_shots = ( ! is_null( $game->home_shots_p1 ) || ! is_null( $game->home_shots_p2 ) || ! is_null( $game->home_shots_p3 ) ||
                     ! is_null( $game->away_shots_p1 ) || ! is_null( $game->away_shots_p2 ) || ! is_null( $game->away_shots_p3 ) );

        if ( $has_shots ) {
            $home_shots_total = intval( $game->home_shots_p1 ?? 0 ) + intval( $game->home_shots_p2 ?? 0 ) + intval( $game->home_shots_p3 ?? 0 );
            $away_shots_total = intval( $game->away_shots_p1 ?? 0 ) + intval( $game->away_shots_p2 ?? 0 ) + intval( $game->away_shots_p3 ?? 0 );
            
            $html .= '<p class="hge-game-shots">';
            $html .= '<strong>' . esc_html__( 'Shots:', 'bunkersnack-game-manager' ) . '</strong> ';
            $html .= $home_shots_total . '-' . $away_shots_total;
            $html .= ' (' . intval( $game->home_shots_p1 ?? 0 ) . '-' . intval( $game->away_shots_p1 ?? 0 ) . ', ';
            $html .= intval( $game->home_shots_p2 ?? 0 ) . '-' . intval( $game->away_shots_p2 ?? 0 ) . ', ';
            $html .= intval( $game->home_shots_p3 ?? 0 ) . '-' . intval( $game->away_shots_p3 ?? 0 ) . ')';
            $html .= '</p>';
        }

        // Location
        if ( ! empty( $game->location ) ) {
            $html .= '<p class="hge-game-location">';
            $html .= '<strong>' . esc_html__( 'Location:', 'bunkersnack-game-manager' ) . '</strong> ';
            $html .= esc_html( $game->location );
            $html .= '</p>';
        }

        // Attendance
        if ( ! is_null( $game->attendance ) && $game->attendance > 0 ) {
            $html .= '<p class="hge-game-attendance">';
            $html .= '<strong>' . esc_html__( 'Attendance:', 'bunkersnack-game-manager' ) . '</strong> ';
            $html .= number_format( intval( $game->attendance ), 0, '.', ' ' );
            $html .= '</p>';
        }

        // Events
        if ( ! empty( $events ) ) {
            // Build assist map
            $assists_by_goal = array();
            $event_count = 0;
            foreach ( $events as $event ) {
                if ( 'assist' !== $event->event_type ) {
                    $event_count++;
                }
                if ( 'assist' === $event->event_type && $event->parent_event_id ) {
                    if ( ! isset( $assists_by_goal[ $event->parent_event_id ] ) ) {
                        $assists_by_goal[ $event->parent_event_id ] = array();
                    }
                    $assists_by_goal[ $event->parent_event_id ][] = array(
                        'name'   => $event->name,
                        'number' => $event->number,
                    );
                }
            }

            $html .= '<div class="hge-events">';
            $html .= '<div class="hge-events-accordion-wrapper">';
            $html .= '<div class="hge-events-accordion-item">';
            
            // Single accordion header for all events
            $html .= '<button class="hge-events-accordion-header">';
            $html .= sprintf(
                esc_html__( 'Game Events', 'bunkersnack-game-manager' ),
                $event_count
            );
            $html .= '<span class="hge-events-accordion-toggle">▼</span>';
            $html .= '</button>';
            
            // Single accordion content containing all events
            $html .= '<div class="hge-events-accordion-content">';

            foreach ( $events as $event ) {
                // Skip assists as they'll be displayed with their goals
                if ( 'assist' === $event->event_type ) {
                    continue;
                }

                // Time - handle NULL values
                $time_display = esc_html__( 'N/A', 'bunkersnack-game-manager' );
                if ( ! is_null( $event->event_time ) && '' !== $event->event_time ) {
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
                }

                $event_label = esc_html( ucfirst( $event->event_type ) );

                // Event item
                $html .= '<div class="hge-event-item hge-event-' . esc_attr( $event->event_type ) . '">';
                $html .= '<p class="hge-event-header">';
                $html .= '<strong>' . sprintf(
                    esc_html__( 'P%d %s - %s', 'bunkersnack-game-manager' ),
                    intval( $event->period ),
                    $time_display,
                     esc_html__( $event_label, 'bunkersnack-game-manager' )
                ) . '</strong>';
                
                if ( $event->name ) {
                    $html .= ' ' . esc_html__( 'by', 'bunkersnack-game-manager' ) . ' <strong>' . esc_html( $event->name );
                    if ( $event->number ) {
                        $html .= ' #' . intval( $event->number );
                    }
                    $html .= '</strong>';
                }

                if ( ! empty( $event->team_shortcode ) ) {
                    $html .= ' <em>(' . esc_html( $event->team_shortcode ) . ')</em>';
                }

                $html .= '</p>';

                // Add assists if this goal has any
                if ( 'goal' === $event->event_type && isset( $assists_by_goal[ $event->id ] ) ) {
                    $assist_names = array();
                    foreach ( $assists_by_goal[ $event->id ] as $assist ) {
                        $assist_display = esc_html( $assist['name'] );
                        if ( $assist['number'] ) {
                            $assist_display .= ' #' . intval( $assist['number'] );
                        }
                        $assist_names[] = $assist_display;
                    }
                    if ( ! empty( $assist_names ) ) {
                        $html .= '<p class="hge-event-assists"><em>' . esc_html__( 'Assists:', 'bunkersnack-game-manager' ) . ' ' . implode( ', ', $assist_names ) . '</em></p>';
                    }
                }

                if ( ! empty( $event->description ) ) {
                    $html .= '<p class="hge-event-description"><em>' . wp_kses_post( $event->description ) . '</em></p>';
                }

                $html .= '</div>';
            }

            $html .= '</div>';
            $html .= '</div>';
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

        // Add styles and scripts
        wp_enqueue_style( 'hge-frontend', HGE_PLUGIN_URL . 'assets/css/frontend.css' );
        wp_enqueue_script( 'hge-frontend', HGE_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), HGE_PLUGIN_VERSION, true );

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
