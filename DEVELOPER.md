# Developer Documentation

## Plugin Architecture

### Core Classes

#### HGE_Database
Handles all database operations.
- **Location**: `includes/class-hge-database.php`
- **Key Methods**:
  - `create_tables()`: Creates database schema on activation
  - `get_all_players()`: Retrieve all players
  - `save_player($data)`: Create or update player
  - `get_game($game_id)`: Get specific game
  - `get_game_events($game_id)`: Get all events for a game
  - `save_event($data)`: Create or update event
  - Static methods for all CRUD operations

#### HGE_Stats
Calculates and manages player statistics.
- **Location**: `includes/class-hge-stats.php`
- **Key Methods**:
  - `update_game_stats($game_id)`: Calculate stats for all players in a game
  - `get_player_stats($player_id, $season)`: Get stats for a specific player
  - `get_season_stats($season, $args)`: Get all stats for a season with sorting

#### HGE_Shortcodes
Handles frontend shortcode registration and display.
- **Location**: `includes/class-hge-shortcodes.php`
- **Shortcodes**:
  - `[hge_game_summary]`: Display game with events
  - `[hge_player_stats]`: Display player statistics table

#### HGE_Admin
Manages admin interface and AJAX endpoints.
- **Location**: `includes/class-hge-admin.php`
- **AJAX Actions**:
  - `hge_save_player`: Save/update player
  - `hge_delete_player`: Delete player
  - `hge_get_player`: Retrieve player data
  - `hge_save_game`: Save/update game
  - `hge_delete_game`: Delete game
  - `hge_get_game`: Retrieve game data
  - `hge_save_event`: Save/update event
  - `hge_delete_event`: Delete event
  - `hge_get_game_events`: Retrieve game events

### Database Tables

```
wp_hge_players
├── id (BIGINT, primary key)
├── name (VARCHAR 100)
├── number (INT 3)
├── position (VARCHAR 50)
├── is_goalie (TINYINT)
└── timestamps

wp_hge_games
├── id (BIGINT, primary key)
├── game_date (DATE)
├── opponent (VARCHAR 100)
├── home_score (INT 3)
├── away_score (INT 3)
├── location (VARCHAR 255)
├── notes (LONGTEXT)
└── timestamps

wp_hge_game_events
├── id (BIGINT, primary key)
├── game_id (BIGINT, FK)
├── player_id (BIGINT, FK)
├── event_type (VARCHAR 50)
├── event_time (INT 3)
├── period (INT 2)
├── description (LONGTEXT)
└── timestamps

wp_hge_player_stats
├── id (BIGINT, primary key)
├── player_id (BIGINT, FK)
├── season (YEAR)
├── games_played (INT 3)
├── goals (INT 3)
├── assists (INT 3)
├── penalty_minutes (INT 3)
├── goals_allowed (INT 3)
├── shots_against (INT 3)
└── timestamps
```

## Adding New Event Types

To add a new event type:

1. **Update the event type dropdown in admin**:
   - Edit `includes/class-hge-admin.php`
   - Find the `#hge-event-type` select in `render_games_page()`
   - Add new `<option value="event_name">`

2. **Update stats calculation**:
   - Edit `includes/class-hge-stats.php`
   - Add case in `update_game_stats()` method:
   ```php
   case 'event_name':
       $players_stats[ $event->player_id ]['field_name']++;
       break;
   ```

3. **Add new database column if needed**:
   - Modify `create_tables()` in `class-hge-database.php`
   - Add migration logic for existing installations

## Adding New Stat Fields

To track a new statistic:

1. **Add column to wp_hge_player_stats table**:
   ```sql
   ALTER TABLE wp_hge_player_stats ADD new_stat INT(3) DEFAULT 0;
   ```

2. **Update stats calculation in HGE_Stats::update_game_stats()**

3. **Add to shortcode display**:
   - Edit `HGE_Shortcodes::player_stats_shortcode()`
   - Add table header and data cell

## Customizing Shortcodes

### Example: Custom Player Stats Display

```php
// Hook into shortcode attributes
add_filter('hge_player_stats_attributes', function($atts) {
    // Modify attributes before processing
    return $atts;
});

// Hook into stats query
add_filter('hge_stats_query_args', function($args) {
    // Modify query arguments
    return $args;
});
```

### Example: Custom Game Summary Display

```php
// Filter shortcode output
add_filter('hge_game_summary_output', function($html, $game_id) {
    // Modify HTML output
    return $html;
}, 10, 2);
```

## Using the Database API

### Get All Players
```php
$players = HGE_Database::get_all_players();
foreach ($players as $player) {
    echo $player->name;
}
```

### Create Player
```php
$player_id = HGE_Database::save_player(array(
    'name' => 'John Doe',
    'number' => 23,
    'position' => 'C',
    'is_goalie' => 0,
));
```

### Get Game Events
```php
$events = HGE_Database::get_game_events(5);
foreach ($events as $event) {
    echo $event->event_type . ' by ' . $event->name;
}
```

### Save Event
```php
$event_id = HGE_Database::save_event(array(
    'game_id' => 5,
    'player_id' => 12,
    'event_type' => 'goal',
    'event_time' => 15,
    'period' => 1,
    'description' => 'Empty netter',
));
```

## Using the Stats API

### Get Player Stats
```php
$stats = HGE_Stats::get_player_stats(12, 2024);
echo 'Goals: ' . $stats->goals;
echo 'Assists: ' . $stats->assists;
```

### Get Season Stats
```php
$season_stats = HGE_Stats::get_season_stats(2024, array(
    'orderby' => 'goals',
    'order' => 'DESC',
));
foreach ($season_stats as $stat) {
    echo $stat->name . ': ' . $stat->goals . ' goals';
}
```

### Update Stats for Game
```php
HGE_Stats::update_game_stats(5); // Recalculate all stats for game 5
```

## Hooks and Filters

The plugin includes various hooks for extensibility:

### Actions
- `hge_after_player_save`: After player is saved
- `hge_after_game_save`: After game is saved
- `hge_after_event_save`: After event is saved, stats updated
- `hge_before_event_delete`: Before event is deleted

### Filters
- `hge_event_types`: Filter available event types
- `hge_player_stats_output`: Filter player stats shortcode output
- `hge_game_summary_output`: Filter game summary shortcode output

## File Structure

```
wordpress-game-events-plugin/
├── hockey-game-events.php          # Main plugin file
├── README.md                        # Original readme
├── PLUGIN_DOCUMENTATION.md          # Full documentation
├── QUICKSTART.md                    # Quick start guide
├── LICENSE                          # GPL-2.0+ license
├── assets/
│   ├── css/
│   │   ├── admin.css               # Admin interface styles
│   │   └── frontend.css            # Frontend shortcode styles
│   └── js/
│       └── admin.js                # Admin interface JS
├── includes/
│   ├── class-hge-admin.php         # Admin interface
│   ├── class-hge-database.php      # Database operations
│   ├── class-hge-shortcodes.php    # Shortcode handlers
│   └── class-hge-stats.php         # Statistics calculation
└── languages/                       # For translations
    └── hockey-game-events.pot       # Translation template
```

## Security Considerations

- All user input is sanitized using WordPress functions
- All output is escaped using appropriate WordPress functions
- AJAX endpoints verify nonce and user capabilities
- SQL queries use prepared statements to prevent injection
- Admin-only operations check `manage_options` capability

## Performance Considerations

1. **Stats Caching**: Player stats are stored in `wp_hge_player_stats` to avoid recalculating on each request
2. **Database Indexes**: Key columns are indexed for faster queries
3. **Lazy Loading**: Stats are only calculated when events are added/modified

## Testing

### Manual Testing Checklist
- [ ] Add new player
- [ ] Edit existing player
- [ ] Delete player
- [ ] Create new game
- [ ] Edit game
- [ ] Delete game
- [ ] Add game event
- [ ] Edit game event
- [ ] Delete game event
- [ ] Verify stats update
- [ ] Test game summary shortcode
- [ ] Test player stats shortcode
- [ ] Test with different seasons
- [ ] Test sorting options
- [ ] Mobile responsive test

## Version History

### 1.0.0 (Current)
- Initial release
- Complete player and game management
- Automatic stat calculation
- Two shortcodes for frontend display
- Admin dashboard with full CRUD operations

## License

GPL-2.0 or later. See LICENSE file.
