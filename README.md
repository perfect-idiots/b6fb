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

## Cơ chế hoạt động của web

### Routing

Trang web là ứng dụng một trang, dựa vào các tham số (parameters) của URL (mảng `$_GET` trong PHP, đối tượng `UrlQuery` trong project này).

**Ví dụ:**
  * `?type=html&page=login` sẽ dẫn đến trang đăng nhập của người dùng thông thường.
  * `?type=html&page=admin` sẽ dẫn đến trang quản trị.
  * `?type=action&action=reset-database` sẽ đặt lại toàn bộ CSDL (bao gồm CSDL MySQL và thư mục `storage`) về trạng thái sơ khai.

#### Một số tham số routing quan trọng

##### `type`

Quy định loại nội dung được trả về

Các giá trị:
  * `html` _(mặc định)_: Server sẽ trả về một trang web
  * `file`: Server sẽ trả về một file (không download)
  * `action`: Server sẽ thực thi một hành động (vd: Xóa một game khỏi CSDL)

##### `page`

Khi `type=html`, quy định trang được trả về. Ngoài ra còn quy định loại tài khoản được đăng nhập (người dùng hay admin).

Các giá trị:
  * `index`: _(mặc định)_: Trang chủ
  * `explore`: Khám phá
  * `profile`: Cài đặt thông tin người dùng
  * `favourite`: Các game mà người dùng đã đánh dấu ưa thích
  * `history`: Các game mà người dùng đã chơi
  * `genre`: Các game thuộc thể loại được xác định bởi tham số `genre`
  * `admin`: Quản trị

Khi `page=admin`, tài khoản admin sẽ được sử dụng để đăng nhập

Khi `type=html&page=admin`, tham số `subpage` xác định trang con của admin để hiển thị, mặc định là `dashboard`.

##### `action`

Chỉ có ý nghĩa khi `type=action`. Quy định hành động được thực thi.

Nếu một hành động yêu cầu quyền quản trị, cần có thêm tham số `page=admin`.
