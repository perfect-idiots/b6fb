select
  games.id as `game_id`,
  unix_timestamp(user_playing_history.date) as `date`,
  games.name as `game_name`,
  user_accounts.fullname as `player_name`,
  user_accounts.username as `player_id`
from user_playing_history
  inner join user_accounts
    on user_playing_history.player_id = user_accounts.username
  inner join games
    on user_playing_history.game_id = games.id
where user_accounts.username = ?
order by user_playing_history.date desc
