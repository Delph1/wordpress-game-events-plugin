# Quick Start Guide

## Installation

1. Copy the `wordpress-game-events-plugin` folder to `/wp-content/plugins/`
2. Go to WordPress Admin Dashboard → Plugins
3. Find "Hockey Game Events" and click "Activate"
4. You'll see a new "Hockey Events" menu item in the admin sidebar

## First Steps

### Step 1: Create Your Players
1. Click **Hockey Events → Players**
2. Fill in the form:
   - **Name**: Player's full name
   - **Jersey Number**: Their jersey number
   - **Position**: Select from C, LW, RW, D, or G
   - **Goalie**: Check this box if they're a goalie
3. Click "Save Player"

### Step 2: Create a Game
1. Click **Hockey Events → Games**
2. Fill in the game details:
   - **Date**: When the game was played
   - **Opponent**: Team name
   - **Location**: Where it was played
   - **Home Score**: Goals scored by your team
   - **Away Score**: Goals scored by opponent
   - **Notes**: Any additional info
3. Click "Save Game"

### Step 3: Log Game Events
1. In the Games list, find your game and click the **"Events"** button
2. A modal will open where you can add events
3. For each event:
   - Select **Event Type**: Goal, Assist, Penalty, Shot Against, Goal Allowed
   - Select the **Player** involved
   - Choose the **Period** and **Time**
   - Add optional description
4. Click "Save Event"
5. Stats will automatically update!

### Step 4: View Statistics
1. Click **Hockey Events → Player Stats**
2. You'll see a table with all calculated stats:
   - For skaters: Games, Goals, Assists, Penalty Minutes
   - For goalies: Games, Shots Against, Goals Allowed
3. Goalie rows are highlighted in gray for easy identification

### Step 5: Display on Your Blog

#### Show a Game Summary
Add this to a post or page where you want to show a specific game:
```
[hge_game_summary game_id="1"]
```
Replace `1` with your game's ID (visible in the Games table)

#### Show Player Statistics
Add this to show the season's player stats:
```
[hge_player_stats]
```

Or customize it:
```
[hge_player_stats season="2024" sortby="goals"]
```

## Key Features

### Automatic Stat Calculation
- Stats are calculated automatically from game events
- Add or delete an event and stats update instantly
- Each calendar year is a separate season

### Penalty Minutes
- When entering a penalty, include the minutes in the description like "2 min" or "5 minutes"
- If not specified, it defaults to 2 minutes

### Multiple Seasons
- Stats are organized by year automatically
- Use `season="2024"` in shortcodes to show different years

## Tips & Tricks

1. **Games Played**: Automatically calculated - a player is counted if they have any event in a game
2. **Edit Players/Games**: Just click the "Edit" button to make changes
3. **Delete Operations**: Deleting a game deletes all its events and recalculates stats
4. **Mobile Friendly**: The stats table is responsive and works on phones

## Common Shortcode Examples

```
[hge_game_summary game_id="5"]
```
Shows game #5 with all events sorted by time

```
[hge_player_stats]
```
Shows current season stats sorted by goals (descending)

```
[hge_player_stats season="2024" sortby="penalty_minutes" sortdir="ASC"]
```
Shows 2024 stats sorted by penalty minutes (ascending)

```
[hge_player_stats sortby="assists"]
```
Shows current season sorted by assists

## Troubleshooting

**Stats not updating?**
- Make sure you saved the event
- Check that the player is selected in the event

**Can't see the shortcode output?**
- Make sure you used the exact game ID
- Try clearing your WordPress cache

**Need to start over?**
- Deactivate the plugin, then reactivate to reset
- Or manually delete the `wp_hge_*` tables from your database
