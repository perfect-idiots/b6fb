insert into user_comments (author_id, game_id, parent_comment_id, content)
select
  input_param.author_id,
  user_comments.game_id,
  input_param.parent_comment_id,
  input_param.content
from (
  select
    ? as author_id,
    ? as parent_comment_id,
    ? as content
) input_param
  inner join user_comments
    on input_param.parent_comment_id = user_comments.id
