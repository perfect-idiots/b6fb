select
  `id`,
  `name`
from games_to_genres
  inner join genres
    on `genre_id` = `id`
where `game_id` = ?
order by `name` asc
