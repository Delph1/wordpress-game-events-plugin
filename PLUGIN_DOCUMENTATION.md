# Hockey Game Events Plugin

A comprehensive WordPress plugin for tracking hockey game events, player statistics, and displaying game summaries and player stats tables using shortcodes.

## Features

### Core Functionality
- **Player Management**: Add, edit, and delete players with details like jersey number, position, and goalie status
- **Game Management**: Create and manage games with dates, opponents, scores, and locations
- **Event Tracking**: Log game events (goals, assists, penalties, shots) for each player
- **Automatic Stats Calculation**: Calculate player statistics based on game events
- **Shortcode Support**: Display game summaries and player statistics on any post or page

### Player Tracking
For **skaters**, the plugin tracks:
- Games Played (GP)
- Goals (G)
- Assists (A)
- Penalty Minutes (PIM)

For **goalies**, the plugin tracks:
- Games Played (GP)
- Shots Against (SA)
- Goals Allowed (GA)

## Installation

1. Download or clone this plugin to your WordPress plugins directory: `/wp-content/plugins/wordpress-game-events-plugin/`
2. Activate the plugin through the WordPress admin dashboard
3. Navigate to **Hockey Events** in the admin menu to start using it

## Usage

### Admin Dashboard

#### Players Management
1. Go to **Hockey Events > Players**
2. Add a new player with:
   - Name (required)
   - Jersey Number
   - Position (C, LW, RW, D, G)
   - Check "Goalie" if applicable
3. Edit or delete existing players

#### Games Management
1. Go to **Hockey Events > Games**
2. Create a new game with:
   - Date (required)
   - Opponent (required)
   - Location
   - Home and Away scores
   - Notes
3. Click **Events** button to manage game events

#### Game Events
1. From the Games page, click **Events** for a specific game
2. Add events by selecting:
   - Event Type: Goal, Assist, Penalty, Shot Against, Goal Allowed
   - Player involved
   - Period and Time
   - Optional description
3. Events are automatically sorted by game time
4. Stats are recalculated automatically when events are added/deleted

#### Player Statistics
1. Go to **Hockey Events > Player Stats**
2. View automatically calculated statistics for all players
3. Goalies are highlighted differently from skaters

### Frontend - Shortcodes

#### Game Summary Shortcode
Display a specific game's summary with all events sorted by game time:

```
[hge_game_summary game_id="123"]
```

**Attributes:**
- `game_id` (required): The ID of the game to display

**Example:**
```
[hge_game_summary game_id="5"]
```

#### Player Statistics Shortcode
Display a sortable table of player statistics for the season:

```
[hge_player_stats season="2025" sortby="goals" sortdir="DESC"]
```

**Attributes:**
- `season` (optional, default: current year): The season/year to display
- `sortby` (optional, default: "goals"): Sort column - options: goals, assists, games_played, penalty_minutes, shots_against
- `sortdir` (optional, default: "DESC"): Sort direction - "ASC" or "DESC"

**Examples:**
```
[hge_player_stats]
[hge_player_stats season="2024" sortby="goals" sortdir="DESC"]
[hge_player_stats sortby="penalty_minutes" sortdir="ASC"]
```

## Database Schema

### Tables Created

- **wp_hge_players**: Stores player information
- **wp_hge_games**: Stores game information
- **wp_hge_game_events**: Stores individual game events (goals, assists, penalties, etc.)
- **wp_hge_player_stats**: Caches calculated player statistics by season

## Event Types

The plugin supports the following event types:

- **goal**: Player scored a goal
- **assist**: Player provided an assist
- **penalty**: Player received a penalty
- **shot_against**: Shot taken against goalie (tracked for goalies)
- **goal_allowed**: Goal scored against goalie (tracked for goalies)

## Styling

The plugin includes CSS for both admin and frontend:

- Admin interface with intuitive forms and tables
- Responsive frontend display
- Different styling for skaters vs. goalies
- Mobile-friendly player stats table

## Permissions

Only users with the `manage_options` capability (administrators) can:
- Add, edit, or delete players
- Add, edit, or delete games
- Add, edit, or delete game events
- View player statistics

## Tips

1. **Setting up a season**: All stats are organized by calendar year. Each January starts a new season.
2. **Penalty minutes**: If you don't specify minutes in the penalty description, it defaults to 2 minutes.
3. **Games played**: Automatically calculated based on unique games where a player has recorded an event.
4. **Stats updates**: Stats are recalculated automatically whenever events are added or deleted.

## Changelog

### Version 1.0.0
- Initial release
- Player management system
- Game event tracking
- Automatic stat calculation
- Game summary shortcode
- Player stats shortcode
- Admin dashboard

## Support

For issues or feature requests, please visit the GitHub repository.

## License

This plugin is licensed under the GPL-2.0+ License.
