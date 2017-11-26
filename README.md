# b6fb

## Tạo tài khoản admin đầu tiên

**Bước 1:** Tạo tài khoản (đăng ký) người dùng thông thường đầu tiên trên giao diện web

**Bước 2:** Chạy lệnh sau trên CSDL

```sql
insert into admin_accounts (username, password_hash)
select username, password_hash
from user_accounts
```

**Kết quả:** Bây giờ bạn đã có một tài khoản admin với tên đăng nhập và mật khẩu tương tự tại khoản người dùng đầu tiên.
