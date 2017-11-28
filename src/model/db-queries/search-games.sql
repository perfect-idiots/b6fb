select
  `id`,
  `name`,
  `genre`,
  `description`
from (
  select
    *,
    (
      (
        if(`id` = `search`, 1, 0) +
        if(`id` like concat('%', `search`, '%'), 1, 0) +
        if(`id` like concat(`search`, ' %'), 1, 0) +
        if(`id` like concat('% ', `search`), 1, 0) +
        if(`id` like concat('% ', `search`, ' %'), 1, 0)
      ) +
      (
        if(`name` = `search`, 1, 0) +
        if(`name` like concat('%', `search`, '%'), 1, 0) +
        if(`name` like concat(`search`, ' %'), 1, 0) +
        if(`name` like concat('% ', `search`), 1, 0) +
        if(`name` like concat('% ', `search`, ' %'), 1, 0)
      )
    ) as by_id_name,
    (
      if(`description` = `search`, 1, 0) +
      if(`description` like concat('%', `search`, '%'), 1, 0) +
      if(`description` like concat(`search`, ' %'), 1, 0) +
      if(`description` like concat('% ', `search`), 1, 0) +
      if(`description` like concat('% ', `search`, ' %'), 1, 0) +
      round(
        (
          length(`description`) -
          length(replace(`description`, `search`, ''))
        ) / length(`search`)
      )
    ) as by_description
  from (
    select *, ? as `search`
    from games
  ) search_table
) relevance
where
  by_id_name + by_description <> 0
order by
  by_id_name desc,
  by_description desc
