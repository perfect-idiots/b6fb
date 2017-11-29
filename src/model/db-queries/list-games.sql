select
  games.id,
  games.name,
  group_concat(genres.id separator ', ') as genre_ids,
  group_concat(genres.name separator ', ') as genre_names,
  games.description
from games
  inner join games_to_genres
    on games.id = games_to_genres.game_id
  inner join genres
    on genres.id = games_to_genres.genre_id
group by game_id
