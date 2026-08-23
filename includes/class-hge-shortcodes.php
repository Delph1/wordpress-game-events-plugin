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

        // Head Referee
        if ( ! empty( $game->head_referee ) ) {
            $html .= '<p class="hge-game-referee">';
            $html .= '<strong>' . esc_html__( 'Head Referee:', 'bunkersnack-game-manager' ) . '</strong> ';
            $html .= esc_html( $game->head_referee );
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

            $running_home_score = 0;
            $running_away_score = 0;
            $events_by_period = array();

            foreach ( $events as $event ) {
                // Calculate running score for goals
                $score_display = '';
                if ( 'goal' === $event->event_type ) {
                    if ( ! empty( $event->team_id ) ) {
                        if ( $event->team_id == $game->home_team_id ) {
                            $running_home_score++;
                        } elseif ( $event->team_id == $game->away_team_id ) {
                            $running_away_score++;
                        }
                    }
                    $score_display = $running_home_score . '-' . $running_away_score . ' ';
                }

                // Skip assists as they'll be displayed with their goals
                if ( 'assist' === $event->event_type ) {
                    continue;
                }

                $period = intval( $event->period );
                if ( ! isset( $events_by_period[ $period ] ) ) {
                    $events_by_period[ $period ] = array();
                }
                $events_by_period[ $period ][] = array(
                    'event'         => $event,
                    'score_display' => $score_display,
                );
            }

            ksort( $events_by_period, SORT_NUMERIC );

            $html .= '<div class="hge-events">';
            $html .= '<div class="hge-events-accordion-wrapper">';
            $html .= '<div class="hge-events-accordion-item">';
            $html .= '<button class="hge-events-accordion-header">';
            $html .= '<span>' . esc_html__( 'Game Events', 'bunkersnack-game-manager' ) . ' (' . intval( $event_count ) . ')</span>';
            $html .= '<span class="hge-events-accordion-toggle">&#9660;</span>';
            $html .= '</button>';
            $html .= '<div class="hge-events-accordion-content">';
            $html .= '<div class="hge-events-period-list">';

            foreach ( $events_by_period as $period => $period_events ) {
                $period_labels = array(
                    1 => __( 'Period 1', 'bunkersnack-game-manager' ),
                    2 => __( 'Period 2', 'bunkersnack-game-manager' ),
                    3 => __( 'Period 3', 'bunkersnack-game-manager' ),
                    4 => __( 'OT', 'bunkersnack-game-manager' ),
                    5 => __( 'SO', 'bunkersnack-game-manager' ),
                );
                $period_label = $period_labels[ $period ] ?? sprintf( __( 'Period %d', 'bunkersnack-game-manager' ), $period );

                $html .= '<section class="hge-events-period-group">';
                $html .= '<h4 class="hge-events-period-title">' . esc_html( $period_label ) . '</h4>';

                foreach ( $period_events as $period_event ) {
                    $event = $period_event['event'];
                    $score_display = $period_event['score_display'];

                    // Time - handle NULL values
                    $time_display = esc_html__( 'N/A', 'bunkersnack-game-manager' );
                    if ( ! is_null( $event->event_time ) && '' !== $event->event_time ) {
                        $event_time_value = intval( $event->event_time );
                        // Always treat as seconds
                        $minutes = intdiv( $event_time_value, 60 );
                        $seconds = $event_time_value % 60;
                        $time_display = $minutes . ':' . str_pad( $seconds, 2, '0', STR_PAD_LEFT );
                    }

                    $event_label = esc_html( ucfirst( $event->event_type ) );

                    $scoring_team = '';
                    if ( 'goal' === $event->event_type ) {
                        if ( $event->team_id == $game->home_team_id ) {
                            $scoring_team = 'home';
                        } elseif ( $event->team_id == $game->away_team_id ) {
                            $scoring_team = 'away';
                        }
                    }

                    $assist_names = array();
                    if ( 'goal' === $event->event_type && isset( $assists_by_goal[ $event->id ] ) ) {
                        foreach ( $assists_by_goal[ $event->id ] as $assist ) {
                            $assist_display = esc_html( $assist['name'] );
                            if ( $assist['number'] ) {
                                $assist_display .= ' #' . intval( $assist['number'] );
                            }
                            $assist_names[] = $assist_display;
                        }
                    }

                    // Event item
                    $html .= '<div class="hge-event-item hge-event-' . esc_attr( $event->event_type ) . '">';
                    $html .= '<div class="hge-event-row">';
                    $html .= '<span class="hge-event-time">' . esc_html( $time_display ) . '</span>';
                    $html .= '<span class="hge-event-type"><strong>' . esc_html( $event_label );

                    if ( 'goal' === $event->event_type ) {
                        $score_parts = explode( '-', trim( $score_display ) );
                        $home_score = $score_parts[0] ?? '0';
                        $away_score = $score_parts[1] ?? '0';
                        $html .= ' <span class="hge-event-score">';
                        $html .= '<span class="' . ( 'home' === $scoring_team ? 'hge-scoring-team' : '' ) . '">' . esc_html( trim( $home_score ) ) . '</span>';
                        $html .= ' - ';
                        $html .= '<span class="' . ( 'away' === $scoring_team ? 'hge-scoring-team' : '' ) . '">' . esc_html( trim( $away_score ) ) . '</span>';
                        $html .= '</span>';
                    } elseif ( 'penalty' === $event->event_type && ! empty( $event->description ) ) {
                        $html .= ' - ' . esc_html( wp_strip_all_tags( $event->description ) );
                    }
                    $html .= '</strong></span>';

                    if ( $event->name || ! empty( $assist_names ) ) {
                        $html .= '<span class="hge-event-player">';
                        if ( $event->name ) {
                            $html .= esc_html( $event->name );
                            if ( $event->number ) {
                                $html .= ' #' . intval( $event->number );
                            }
                        }
                        if ( ! empty( $assist_names ) ) {
                            $html .= ' <em>(' . esc_html__( 'Assists:', 'bunkersnack-game-manager' ) . ' ' . implode( ', ', $assist_names ) . ')</em>';
                        }
                        $html .= '</span>';
                    } else {
                        $html .= '<span class="hge-event-player">&nbsp;</span>';
                    }

                    if ( ! empty( $event->team_shortcode ) ) {
                        $html .= '<span class="hge-event-team">' . esc_html( $event->team_shortcode ) . '</span>';
                    } else {
                        $html .= '<span class="hge-event-team">&nbsp;</span>';
                    }

                    $html .= '</div>';

                    if ( ! empty( $event->description ) && 'penalty' !== $event->event_type ) {
                    $html .= '<p class="hge-event-description"><em>' . wp_kses_post( $event->description ) . '</em></p>';
                    }

                    $html .= '</div>';
                }

                $html .= '</section>';
            }

            $html .= '</div>';
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
