select
  games.id as game_id,
  genres.id as genre_id,
  games.name as game_name,
  games.description as game_description,
  genres.name as genre_name
from games_to_genres
  inner join games
    on game_id = games.id
  inner join genres
    on genre_id = genres.id
