# Bunkersnack Game Manager

A comprehensive WordPress plugin for managing hockey game events, player statistics, and displaying game summaries with period-by-period statistics.

## Features

- **Game Management**: Create and manage games with teams, dates, locations, and attendance
- **Event Tracking**: Record game events including goals, assists, penalties, shots, and goals allowed
- **Player Statistics**: Automatically calculated player statistics based on game events
- **Accordion Interface**: Collapsible accordion-style display of game events on the frontend
- **Statistics Display**: Period-by-period goals and shots breakdown
- **Team & Player Management**: Manage team rosters with player positions and jersey numbers
- **Season Management**: Organize games by season and league
- **Shortcodes**: Easy frontend display with `[hge_game_summary]` and `[hge_player_stats]` shortcodes
- **Multi-language Support**: Includes Swedish (sv_SE) translations

## Installation

1. Download or clone this repository to your WordPress plugins directory
2. Activate the plugin through the WordPress admin panel
3. Use the "Hockey Events" menu to manage seasons, teams, players, and games

## Usage

### Game Summary Shortcode
Display a complete game summary with all events:
```
[hge_game_summary game_id="123"]
```

### Player Statistics Shortcode
Display player statistics for a season:
```
[hge_player_stats season="2024-2025"]
```

## Requirements

- WordPress 5.0+
- PHP 7.4+
- jQuery (for accordion functionality)
