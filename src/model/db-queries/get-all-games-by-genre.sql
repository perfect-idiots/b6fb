select
  `id`,
  `name`,
  `description`
from games_to_genres
  inner join games
    on `game_id` = `id`
where `genre_id` = ?
order by `name` asc
