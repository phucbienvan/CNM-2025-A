## 🚀 Các bước chạy khi clone về

### Bước 1: Clone repository
```bash
git clone https://github.com/phucbienvan/CNM-2025-B.git
cd CNM-2025-B
```

### Bước 2: Cài đặt dependencies
```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Node.js dependencies (nếu cần)
npm install
```

### Bước 3: Cấu hình môi trường
```bash
# Copy file env mẫu
cp .env.example .env

# Tạo application key
php artisan key:generate
```

### Bước 4: Cấu hình database
Chỉnh sửa file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cnm_2025_b
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 5: Chạy migrations
```bash
php artisan migrate
```

### Bước 6: Chạy ứng dụng
```bash
# Chạy Laravel development server
php artisan serve

# Hoặc chạy trên port khác
php artisan serve --port=8080
```

## 🔧 Troubleshooting

### Lỗi thường gặp:

#### 1. **Database connection error**
```bash
# Kiểm tra cấu hình database trong .env
# Đảm bảo database server đang chạy
# Tạo database nếu chưa có
mysql -u root -p
CREATE DATABASE cnm_2025_b;
```

#### 2. **Class not found**
```bash
# Chạy composer dump-autoload
composer dump-autoload
```

#### 3. **Permission denied**
```bash
# Cấp quyền write cho thư mục storage và cache
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

#### 4. **Application key not set**
```bash
# Tạo application key
php artisan key:generate
```

#### 5. **Clear cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear

hoặc

php artisan optimize
```

## 🤝 Đóng góp

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add some amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Tạo Pull Request