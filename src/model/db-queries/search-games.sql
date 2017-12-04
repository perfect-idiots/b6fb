select
  `id`,
  `name`,
  `description`
from (
  select
    *,
    (
      2 * (
        if(binary `id` = `search`, 1, 0) +
        if(binary `id` like concat('%', `search`, '%'), 1, 0) +
        if(binary `id` like concat(`search`, ' %'), 1, 0) +
        if(binary `id` like concat('% ', `search`), 1, 0) +
        if(binary `id` like concat('% ', `search`, ' %'), 1, 0)
      ) +
      2 * (
        if(binary `name` = `search`, 1, 0) +
        if(binary `name` like concat('%', `search`, '%'), 1, 0) +
        if(binary `name` like concat(`search`, ' %'), 1, 0) +
        if(binary `name` like concat('% ', `search`), 1, 0) +
        if(binary `name` like concat('% ', `search`, ' %'), 1, 0)
      ) +
      (
        if(lower(`id`) = lower(`search`), 1, 0) +
        if(lower(`id`) like concat('%', lower(`search`), '%'), 1, 0) +
        if(lower(`id`) like concat(lower(`search`), ' %'), 1, 0) +
        if(lower(`id`) like concat('% ', lower(`search`)), 1, 0) +
        if(lower(`id`) like concat('% ', lower(`search`), ' %'), 1, 0)
      ) +
      (
        if(lower(`name`) = lower(`search`), 1, 0) +
        if(lower(`name`) like concat('%', lower(`search`), '%'), 1, 0) +
        if(lower(`name`) like concat(lower(`search`), ' %'), 1, 0) +
        if(lower(`name`) like concat('% ', lower(`search`)), 1, 0) +
        if(lower(`name`) like concat('% ', lower(`search`), ' %'), 1, 0)
      )
    ) as by_id_name,
    (
      2 * (
        if(binary `description` = `search`, 1, 0) +
        if(binary `description` like concat('%', `search`, '%'), 1, 0) +
        if(binary `description` like concat(`search`, ' %'), 1, 0) +
        if(binary `description` like concat('% ', `search`), 1, 0) +
        if(binary `description` like concat('% ', `search`, ' %'), 1, 0) +
        round(
          (
            length(`description`) -
            length(replace(`description`, `search`, ''))
          ) / length(`search`)
        )
      ) +
      (
        if(lower(`description`) = lower(`search`), 1, 0) +
        if(lower(`description`) like concat('%', lower(`search`), '%'), 1, 0) +
        if(lower(`description`) like concat(lower(`search`), ' %'), 1, 0) +
        if(lower(`description`) like concat('% ', lower(`search`)), 1, 0) +
        if(lower(`description`) like concat('% ', lower(`search`), ' %'), 1, 0) +
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
  by_id_name + by_description <> 0
order by
  by_id_name desc,
  by_description desc
