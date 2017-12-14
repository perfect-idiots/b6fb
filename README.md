# b6fb

## Vị trí của ứng dụng trong project

Toàn bộ code của ứng dụng được đặt trong thư mục `src/`.

> 📓 **Ví dụ:** Giả sử project được copy vào thư mục `htdocs/b6fb/` (`C:\xampp\htdocs\b6fb\` trên Windows hoặc `/opt/lampp/htdocs/b6fb/` trên Linux), thì:
>   * Code nằm trong thư mục `htdocs/b6fb/src/`
>   * Địa chỉ localhost của trang web là `http://localhost/b6fb/src/`
>     - Trang Quản trị: `http://localhost/b6fb/src/?page=admin`
>     - Trang chủ: `http://localhost/b6fb/src/?page=index`

## Hướng dẫn cài đặt

### Yêu cầu hệ thống

#### Server

XAMPP với **PHP 7**

> ⚠ **Chú ý:** Trang web này không hoạt động với phiên bản XAMPP thông thường (PHP 5) mà yêu cầu PHP 7 để hoạt động.
>
> Download XAMPP 7.1: [Windows](https://www.apachefriends.org/xampp-files/7.1.11/xampp-win32-7.1.11-0-VC14-installer.exe) | [Linux](https://www.apachefriends.org/xampp-files/7.1.11/xampp-linux-x64-7.1.11-0-installer.run) | [macOS](https://www.apachefriends.org/xampp-files/7.1.11/xampp-osx-7.1.11-0-installer.dmg)

#### Trình duyệt

> ⚠ **Chú ý:** Do được lập trình dựa trên nền tảng HTML 5 + CSS 3 + ECMAScript 6 nên chỉ có những trình duyệt mới nhất mới có thể hiện thị trang web.

> ⚠ **Chú ý:** Công nghệ Flash đang dần bị thay thế bởi HTML5 nên chỉ một số trình duyệt desktop mới có thể chạy được game.

**Khuyến cáo:**
  * Google Chrome
  * Firefox + Flash Plugin

**Không khuyến cáo:**
  * ~~Internet Explorer~~ _(không hỗ trợ ECMAScript 6)_
  * ~~Microsoft Edge~~ _(không hỗ trợ Flash)_
  * ~~Mobile Phone~~ _(không hỗ trợ Flash)_

### Bước 1: Chuẩn bị Cơ sở Dữ liệu (MySQL)

**Bước 1.1:** Mở `http://localhost/phpmyadmin`.

**Bước 1.2:** Import file `sql/schema.sql` (hoặc copy nội dung của file đó vào ô nhập lệnh SQL).

> ⮕ Cơ sở dữ liệu được tạo có tên `b6fb`

**Bước 1.3:** Tạo file `src/model/database/database.php` (tránh nhầm với `src/model/database.php`) với nội dung như sau:

```php
<?php
return [
  'host' => 'localhost',
  'username' => 'root',
  'password' => '',
  'dbname' => 'b6fb',
];
?>
```

### Bước 2: Tạo tài khoản admin

> Để đảm bảo tính an toàn, tài khoản admin không thể được đăng ký từ website mà phải được thêm trực tiếp vào CSDL bằng câu lệnh SQL.

**Bước 2.1:** Mở `http://localhost/phpmyadmin`

**Bước 2.2:** Nhập câu lệnh sql sau:

```sql
insert into admin_accounts (username, password_hash)
values (
 'admin', -- Tên đăng nhập là 'admin'
 '$2y$10$dPsN3Gw7lQR1BAA9xJCNs.hUfMT5lkGnrtO4g44wefiXFN/SPdJ8u' -- Mật khẩu là '123456789'
)
```

**Bước 2.3:** Đổi mật khẩu cho tài khoản admin:

_Bước 2.3.1:_ Vào trang `src/?page=admin` (Quản trị), giao diện đăng nhập sẽ hiện ra.

_Bước 2.3.2:_ Nhập thông tin sau vào form đăng nhập:

> Tên đăng nhập: `admin`
> Mật khẩu: `123456789`

**Bước 2.4:** Chọn "Nâng cao" → "Đổi mật khẩu".

### Bước 3: Nhập dữ liệu

#### Phương pháp 1: Nhập thủ công

**Bước 3.1.3:** Thêm thể loại:

Truy cập mục "Trò chơi" của trang Quản trị, nhấn nút "Thêm thể loại", và nhập đầy đử các thông tin cần thiết.

**Bước 3.1.2:** Thêm trò chơi:

Truy cập mục "Trò chơi" của trang Quản trị, nhấn nút "Thêm trò chơi", và nhập **đầy đủ** các thông tin cần thiết.

> ⚠ **Chú ý:** Mỗi trò chơi _phải_ có ít nhất 1 thể loại thì mới được liệt kê.

> 🕮 **Tip:** Mục "mô tả" của trò chơi hỗ trợ _một số_ cú pháp [markdown](https://goo.gl/vnWvnJ) (chữ đậm, chữ nghiêng, link, html ...).

#### Phương pháp 2: Nhập hàng loạt

> ⚠ **Chú ý:** Phương pháp này sẽ sao chép tất cả các file từ `src/media/` sang `src/storage/` nên sẽ mất một khoảng thời gian (từ 1 đến 2 phút).

**Bước 3.2.1:** Truy cập mục "Nâng cao" của trang Quản trị.

**Bước 3.2.2:** Đánh dấu tick (`✓`) vào mục "Dữ liệu Trò chơi".

**Bước 3.2.3:** Nhấn nút "Đặt lại CSDL".

**Bước 3.2.4:** Nhập mật khẩu admin, nhấn "Xóa và Đặt lại CSDL", và chờ 1 - 2 phút.

> ⮕ Khi hoàn tất, trình duyệt sẽ trở về giao diện "Nâng cao".

### Hoàn tất cài đặt

Truy cập Trang Chủ bằng cách truy cập `src/?page=index` hoặc `src/`.

> ⚠ **Chú ý:** Trang Chủ (`src/?page=index`) và Trang Quản trị (`src/?page=admin`) không được kết nối với nhau vì người dùng thông thường không bao giờ dùng trang quản trị.

## Cơ chế hoạt động của web

### Routing

Trang web là ứng dụng một trang, dựa vào các tham số (parameters) của URL (mảng `$_GET` trong PHP, đối tượng `UrlQuery` trong project này).

**Ví dụ:**
  * `src/?type=html&page=login` sẽ dẫn đến trang đăng nhập của người dùng thông thường.
  * `src/?type=html&page=admin` sẽ dẫn đến trang quản trị.
  * `src/?type=action&action=reset-database` sẽ đặt lại toàn bộ CSDL (bao gồm CSDL MySQL và thư mục `storage`) về trạng thái sơ khai.

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

## Cấu trúc code

### Mô hình lập trình

#### Programming Paradigm

##### Object Oriented Programming

Chương trình được chia thành các class.

Một số class/interface được thừa kế nhiều:
  * `interface DataContainer`.
    * `class RawDataContainer`.
    * `abstract class LazyLoadedDataContainer`.
  * `interface Component`.

Một số class/interface được sử dụng nhiều:
  * `class HtmlElement`

##### Functional Programming

Chương trình được lập trình theo các nguyên tắc của Functional Programming
  * Cấu tạo từ tập hợp các biểu thức (expression):
    * Có thể coi chương trình là một biểu thức lớn chứa các biểu thức nhỏ, các biểu thức nhỏ này lại chứa các biểu thức nhỏ hơn.
  * Giá trị bất biến (immutable):
    * Đa số các biến là bất biến.
    * Tất cả các đối tượng là bất biến.
  * Hạn chế sự sử dụng của lệnh gán và control flow:
    * Lệnh gán duy nhất được thực hiện là phép khởi tạo.
    * Thay thế lệnh `if` bằng ternary (`a ? b : c`) khi có thể.
    * Thay thế vòng lặp `for`, `while`, `foreach` bằng `array_map`/`array_reduce` khi có thể.
  * Hạn chế hiệu ứng bên (side-effect):
    * Không dùng hiệu ứng bên dưới dạng output của một hàm hay phương thức.
    * Hiệu ứng bên (nếu có) không ảnh hưởng đến kết quả của bất kỳ một hàm hay phương thức nào.
    * Thông thường sau một lệnh tạo hiệu ứng bên, chương trình kết thúc: reload, redirect, hoặc exit.
  * Một hàm hay phương thức chỉ phụ thuộc vào giá trị bất biến:
    * Truyền tham số theo giá trị (pass-by-value), không truyền theo tham chiếu (pass-by-reference)
    * Nếu một phạm vi (scope) hay clojure không bất biến, hàm hay phương thức mà phụ thuộc scope hay clojure đó chỉ phụ thuộc vào snapshot tại một thời điểm của scope/clojure.
  * Kết quả có thể dự đoán được (predictable):
    * Với mỗi tập hợp input, hàm chỉ trả về một giá trị nhất định.
    * Các hàm code side-effect không trả về kết quả cho hàm tiếp theo (`void`).

Chương trình không hoàn toàn là (Pure) Functional Programming
  * Thiết kế của PHP là Imperative Programming:
    * PHP được thiết kế theo mô hình Lập Trình Hướng Thủ Tục (Procedural Programming), còn dựa nhiều vào việc thay đổi giá trị các biến.
    * Pure Functional Programming trên PHP là không khả thi.
  * Việc sử dụng control flow kết hợp với thay đổi giá trị của một biến vẫn được sử dụng nếu giải pháp functional programming dẫn đến việc đọc/hiểu code khó khăn.
    * Chỉ sử dụng trong phạm vị cục bộ (local-scope), trạng thái toàn cục (global state) vẫn phải bất biến.
    * Không thay đổi trạng thái hay thuộc tính của một đối tượng. Thay vào đó, tạo một đối tượng mới.
    * Các trạng thái biến thiên không được chia sẻ giữa các hàm hay phương thức cũng như giữa các lần gọi hàm hay phương thức.
  * Vẫn sử dụng các hàm tạo side-effect vì nó thuận tiện và đơn giản hơn việc truyền dữ liệu lên top.
    * Các hàm này thường được dùng để cập nhật Cookie, Session, và CSDL.
    * Sau việc gọi các hàm này, chương trình kết thúc (reload, redirect, hoặc exit).

##### Object Oriented Programming ⨉ Functional Programming

Trạng thái (thuộc tính) của một đối tượng không thay đổi (stateless object).

Các phương thức biến đối (transform) của một class không thay đổi (mutate) thuộc tính của một đối tượng mà thay vào đó tạo ra một đối tượng mới với thuộc tính mong muốn. Ví dụ:

```php
<?php
class Foo {
  private $value; // thuộc tính (trạng thái) của đối tượng
  public function __construct(int $value); // hàm khởi tạo
  public function get(): int; // hàm lấy giá trị

  // ví dụ 1
  public function add(int $addend): self {
    return new static($this->get() + $addend);
  }

  // ví dụ 2
  public function mul(int $factor): self {
    return new static($this->get() * $factor);
  }

  // ví dụ 3
  public function exp(int $exponent): self {
    return new static(pow($this->get(), $exponent));
  }
}
?>
```

#### Một trang

Ứng dụng web chỉ có một tệp php duy nhất mà trình duyệt có thể truy cập trực tiếp: `src/index.php`.

Tệp `index.php` này sẽ quyết định nội dung được trả về cũng như các hành động được thực thi.

#### Model-View-Controller

##### Phần Model

Nằm trong thư mục `src/model`. File chính là `src/model/index.php`.

Vai trò:
  * Cung cấp tài nguyên cho Controller để thao tác với dữ liệu đầu vào (`$_GET`, `$_POST`, ...).
  * Cung cấp các câu lệnh để thao tác với CSDL MySQL.

Đặc điểm:
  * Singleton: Mỗi class trong model chỉ được thực thể hóa một lần duy nhất trong Controller (cụ thể là hàm `main` trong `src/controller/index.php`).
  * Tất cả các class trong model đều có interface là `DataContainer` (thừa kế từ `RawDataContainer` hoặc `LazyLoadedDataContainer`).

##### Phần View

Nằm trong thư mục `src/view`. File chính là `src/view/index.php`.

Vai trò:
  * Nội dung của trang web khi `type=html`.

Đặc điểm:
  * Phần lớn nội dung được cấu tạo từ các [component](#component).
  * Đa phần các component thừa kế từ `RawDataContainer`.

##### Phần Controller

Nằm trong thư mục `src/controller`. File chính là `src/controller/index.php`. Hàm chính là `function main(): string`.

Vai trò:
  * Gọi file `src/model/index.php` và file `src/view/index.php` nhưng được gọi bởi file `src/index.php`.
  * Tạo ra các thực thể (đối tượng) của các class từ Model (một thực thể trên một class) và truyến chúng đến View.
  * Điều khiển routing.
  * Bảo mật: Tất cả các class bảo mật đều được thừa kế từ class `Security` trong file `src/controller/security.php`.
  * Thực thi các câu lệnh liên quan đến CSDL (các lệnh này được cung cấp bởi Model).

#### Component

Giao diện ứng dụng được cấu tạo từ các thành phần nhỏ hơn được gọi là các component.

Các file và thư mục:
  * `src/lib/component-base.php`: Các interface, class, component cơ bản.
  * `src/lib/render.php`: Phương thức `Renderer::render` chuyển đổi một component thành một chuỗi HTML/XML.
  * `src/view/components`: Các component được dùng trong chương trình này.

Các class, interface, component cơ bản:
  * `interface Component`: Tất cả các class/component cơ bản cũng như các component thường đều cài đặt (implements) interface này.
  * `abstract class PrimaryComponent implements Component`: Class chung của các component cơ bản.
    * Các component thường không được phép trực tiếp hay dán tiếp thừa kế (extends) class này.
  * `abstract class Element extends PrimaryComponent`: Class chung của các phần tử HTML/XML.
    * `class HtmlElement extends Element`: Phần tử HTML.
    * `class XmlElement extends Element`: Phần tử XML.
  * `abstract class TextBase extends PrimaryComponent`: Class chung của các thành phần văn bản.
    * `class TextNode extends TextBase`: Văn bản đã được xử lý qua hàm `htmlspecialchars`. Dùng class này để hiển thị văn bản thông thường cũng như những văn bản không an toàn. Đây là class được chọn khi một chuỗi (string) được truyền trực tiếp vào `HtmlElement`/`XmlElement`.
    * `class UnescapedText extends TextBase`: Văn bản sẽ được giữ nguyên, không qua xử lý của hàm `htmlspecialchars`. Chỉ dùng class này khi chuỗi được truyền vào là từ nguồn tin cậy.

#### Dòng dữ liệu (Data Flow)

Dữ liệu được truyền giữa các class con của `DataContainer`.

Có 3 dòng dữ liệu:
  * Dòng dữ liệu chính (MVC): Bắt nguồn từ Model, đi qua Controller, kết thúc tại View.
  * Dòng dữ liệu con: Được truyền dưới dạng phần tử con của dòng chính
    * `UrlQuery`: DataContainer của mảng `$_GET`.
    * `HttpData`: DataContainer của mảng `$_POST`.
    * `Cookie`: DataContainer của mảng `$_COOKIE`.
    * `Session`: DataContainer của mảng `$_SESSION`.
  * Các dung hợp giữa CSDL và `LoginDoubleChecker`:
    * Mỗi class thừa kế `LoginDoubleChecker` (`src/controller/security.php`) và có chứa `DatabaseQuerySet` (`src/model/database.php`).
    * Để đảm bảo tính bảo mật, mọi thao tác với CSDL đều thông qua một trong các dòng này.

Một dòng dữ liệu trải qua 3 quá trình chính:
  * Quá trình khai báo dưới dạng class:
    * Dòng chính, các dòng con, dòng CSDL được khai báo tại Model.
    * Riêng `LoginDoubleChecker` được khai báo tại `src/controller/security.php`.
  * Quá trình thực thể hóa và lắp ráp:
    * Tất cả được thực thể hóa tại hàm `main` trong file `src/controller/index.php`.
  * Quá trình sử dụng và chuyển tiếp dưới dạng thực thể:
    * Được chuyển tiếp tại các hàm `sendHtml`, `switchPage`, ...
    * Được sử dụng tại `sendAction` và `sendFile`.
    * Được chuyển tiếp và sử dụng tại View.
