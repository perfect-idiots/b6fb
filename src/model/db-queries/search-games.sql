select
  `id`,
  `name`,
  `description`
from (
  select
    *,
    (
      2 * if(binary `id` = `search`, 1, 0) +
      2 * if(binary `name` = `search`, 1, 0) +
      if(lower(`id`) = lower(`search`), 1, 0) +
      if(lower(`name`) = lower(`search`), 1, 0)
    ) as by_exact_id_name,
    (
      2 * (
        1 * if(binary `id` like concat('%', `search`, '%'), 1, 0) +
        2 * if(binary `id` like concat(`search`, '-%'), 1, 0) +
        2 * if(binary `id` like concat('%-', `search`), 1, 0) +
        3 * if(binary `id` like concat('%-', `search`, '-%'), 1, 0)
      ) +
      2 * (
        1 * if(binary `name` like concat('%', `search`, '%'), 1, 0) +
        2 * if(binary `name` like concat(`search`, ' %'), 1, 0) +
        2 * if(binary `name` like concat('% ', `search`), 1, 0) +
        3 * if(binary `name` like concat('% ', `search`, ' %'), 1, 0)
      ) +
      (
        1 * if(lower(`id`) like concat('%', lower(`search`), '%'), 1, 0) +
        2 * if(lower(`id`) like concat(lower(`search`), '-%'), 1, 0) +
        2 * if(lower(`id`) like concat('%-', lower(`search`)), 1, 0) +
        3 * if(lower(`id`) like concat('%-', lower(`search`), '-%'), 1, 0)
      ) +
      (
        1 * if(lower(`name`) like concat('%', lower(`search`), '%'), 1, 0) +
        2 * if(lower(`name`) like concat(lower(`search`), ' %'), 1, 0) +
        2 * if(lower(`name`) like concat('% ', lower(`search`)), 1, 0) +
        3 * if(lower(`name`) like concat('% ', lower(`search`), ' %'), 1, 0)
      )
    ) as by_like_id_name,
    (
      2 * (
        round(
          (
            length(`description`) -
            length(replace(`description`, `search`, ''))
          ) / length(`search`)
        )
      ) +
      (
        round(
          (
            length(`description`) -
            length(replace(lower(`description`), lower(`search`), ''))
          ) / length(lower(`search`))
        )
      )
    ) as by_description
  from (
    select *, ? as `search`
    from games
  ) search_table
) relevance
where
  by_exact_id_name <> 0 or
  by_like_id_name <> 0 or
  by_description
order by
  by_exact_id_name desc,
  by_like_id_name desc,
  by_description desc
