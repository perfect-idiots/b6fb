select *
from (
  select
    games.name,
    group_concat(genres.name separator ', ') as genre_names,
    group_concat(genres.id separator ', ') as genre_ids,
    games.description,
    games.id
  from games
    inner join games_to_genres
      on game_id = games.id
    inner join genres
      on genre_id = genres.id
  where
    games.id = ?
) bridge
where
  bridge.id is not null
