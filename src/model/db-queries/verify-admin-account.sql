select count(*) as ok
from admin_accounts
where username = ? and password_hash = ?
