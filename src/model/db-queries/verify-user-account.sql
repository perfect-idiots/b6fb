select count(*) as ok
from user_accounts
where username = ? and password_hash = ?
