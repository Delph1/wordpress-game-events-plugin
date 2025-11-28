# Feature Overview

## What This Plugin Does

The Hockey Game Events plugin is a complete solution for managing hockey game statistics and displaying them on your WordPress blog.

## Use Cases

### Use Case 1: Hockey Team Blog
Track all games throughout the season and display:
- Game summaries on game recap posts
- Current season player statistics on a "Stats" page
- Easy update workflow for team managers

### Use Case 2: Hockey History Archive
Maintain a historical record of:
- Past seasons' games and events
- Player statistics by year
- Game details and notes

### Use Case 3: Multi-Team Sports Blog
Manage multiple teams by:
- Creating separate players per team
- Tracking different seasons per year
- Displaying stats by season on dedicated pages

## Core Features Explained

### Player Management

**What You Can Do:**
- Create player profiles with name, jersey number, position
- Mark players as skaters or goalies
- Edit player information at any time
- Delete players (automatically removes associated events and stats)

**Data Stored:**
- Player name
- Jersey number
- Position (Center, Left Wing, Right Wing, Defenseman, Goalie)
- Goalie status

**Why It Matters:**
- Provides a clean roster system
- Differentiates between skater and goalie tracking
- Allows quick identification of players

### Game Management

**What You Can Do:**
- Create games with date, opponent, location
- Enter final scores (home/away)
- Add notes about the game
- Edit or delete games
- Manage events for each game

**Data Stored:**
- Game date
- Opponent name
- Location where played
- Final scores
- Additional notes

**Why It Matters:**
- Creates historical record of all games
- Easy reference for blog posts
- Organized by date for chronological viewing

### Event Tracking

**Supported Event Types:**

For All Players:
- **Goal**: Player scored
- **Assist**: Player helped score

For Skaters:
- **Penalty**: Player received penalty (specify minutes)

For Goalies:
- **Shot Against**: Shot faced by goalie
- **Goal Allowed**: Goal scored against goalie

**What You Can Do:**
- Log each event with exact time and period
- Assign events to specific players
- Add descriptions for context
- View all events sorted by time
- Edit or delete events

**Why It Matters:**
- Creates detailed game-by-game breakdown
- Basis for all statistical calculations
- Allows fans to understand game flow

### Automatic Statistics

**Calculated Stats:**

For Skaters:
- Games Played: Count of games with at least one event
- Goals: Total goals scored
- Assists: Total assists recorded
- Penalty Minutes: Total penalty time accumulated

For Goalies:
- Games Played: Count of games with at least one event
- Shots Against: Total shots faced
- Goals Allowed: Total goals conceded

**How It Works:**
1. When you add/edit/delete an event, stats recalculate
2. Only games with recorded events count toward "Games Played"
3. Stats are cached in database for fast retrieval
4. Each calendar year is a separate season

**Why It Matters:**
- No manual stat entry needed
- Always accurate
- Easy sorting and filtering
- Ready to display to fans

### Frontend Display

#### Game Summary Shortcode

Shows a complete game recap:

```
[hge_game_summary game_id="5"]
```

**Displays:**
- Game date and opponent
- Final score
- Location
- All events sorted by period and time
- Event details (player, type, time, notes)
- Game notes

**Example Output:**
```
December 15, 2024 vs Hometown Hawks
Final Score: 4 - 2
Location: Local Arena

Game Events
P1 05:00 GOAL - John Smith #23
P1 12:30 ASSIST - Mike Johnson #7
P2 08:45 PENALTY - Tom Wilson #14 (2 min)
...
```

#### Player Statistics Shortcode

Shows sortable season statistics:

```
[hge_player_stats]
```

**Displays:**
- Player name and number
- Position
- Games played
- Goals, Assists, Penalty minutes (skaters)
- Shots against, Goals allowed (goalies)
- Sortable by different columns
- Different styling for goalies

**Example Output:**
```
Player        #  Pos  GP  G   A   PIM  SA  GA
John Smith   23  C    20  15  12   8   -   -
Mike Johnson  7  LW   20  8   14   6   -   -
Tom Wilson   14  D    19  2   5   14   -   -
Dave Brown    1  G    18  -   -    -   156  8
```

**Sorting Options:**
```
[hge_player_stats sortby="goals"]              # By goals
[hge_player_stats sortby="assists"]            # By assists
[hge_player_stats sortby="penalty_minutes"]    # By penalties
[hge_player_stats sortby="shots_against"]      # By shots (goalies)
```

**Multiple Seasons:**
```
[hge_player_stats season="2024"]  # Show 2024 stats
[hge_player_stats season="2023"]  # Show 2023 stats
```

## Admin Dashboard Features

### Player Management Interface
- **Add Form**: Streamlined form to add new players
- **Player List**: Table showing all players with quick edit/delete
- **Edit In-Place**: Load player data into form with one click
- **Bulk Delete**: Remove players easily

### Games Management Interface
- **Add Form**: Create games with all details
- **Games List**: Chronological list with scores
- **Quick Actions**: Edit, manage events, delete in one place
- **Events Modal**: Pop-up modal to manage game events

### Statistics Dashboard
- **Auto-Calculated**: All stats calculated from events
- **Season View**: See all stats for current year
- **Sorting**: Data sorted by goals, assists, or other metrics
- **Visual Distinction**: Goalies highlighted differently

## Workflow Example

### Season Setup
1. Go to **Hockey Events → Players**
2. Add all team roster players
3. Set positions and jersey numbers
4. Mark goalies appropriately

### During Season
1. After each game, go to **Hockey Events → Games**
2. Create new game with date and opponent
3. Click **Events** to add game events
4. Add each goal, assist, penalty as they occurred
5. Stats automatically update

### After Season
1. View complete statistics at **Hockey Events → Player Stats**
2. Display on blog using `[hge_player_stats]`
3. Keep stats archived for history

### Blog Integration
1. Create game recap post
2. Add `[hge_game_summary game_id="5"]` for game details
3. Create season stats page with `[hge_player_stats]`
4. Link between blog posts and stats

## Key Advantages

### For Administrators
- Easy one-click entry of game events
- No manual stat calculations required
- Edit or correct data at any time
- Complete audit trail of all events

### For Readers
- Detailed game recaps with play-by-play
- Current season statistics always available
- Easy comparison between players
- Mobile-friendly presentation

### For Developers
- Clean plugin architecture
- Well-documented code
- Extensible for custom features
- Use standard WordPress patterns

## Real-World Example

**Scenario**: Managing a university women's hockey team blog

**Setup:**
1. Add all 23 roster players (18 skaters, 5 goalies)
2. Mark 5 players as goalies

**Game Day:**
1. Record all goals with time and assist
2. Log penalties with duration
3. For each goalie appearance: record shots and goals

**After Game:**
1. Game summary auto-populates with all events
2. Player stats instantly update
3. Can post blog post with `[hge_game_summary game_id="1"]`

**End of Season:**
1. View complete season stats: `[hge_player_stats]`
2. Create "Season in Review" post
3. Sort by different categories (goals, penalties, etc.)
4. Archive all game records for history

## Supported Statistics

### Skater Statistics
- **GP (Games Played)**: How many games did they play in?
- **G (Goals)**: How many goals did they score?
- **A (Assists)**: How many goals did they help on?
- **PIM (Penalty Minutes)**: How much penalty time accumulated?

### Goalie Statistics
- **GP (Games Played)**: How many games did they play in?
- **SA (Shots Against)**: How many shots did they face?
- **GA (Goals Allowed)**: How many goals did they give up?

### Future Enhancement Possibilities
- Plus/Minus tracking
- Shooting percentage (GP calculation)
- Goals Against Average (GAA)
- Save percentage
- Shifts per game
- Time on ice
- Advanced stats integration
