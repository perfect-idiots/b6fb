# b6fb

## Create first admin account

**Step 1:** Create first user account

**Step 2:** Run the following query in your database

```sql
insert into admin_accounts (username, password_hash)
select username, password_hash
from user_accounts
```
