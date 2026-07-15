# SQL 查询页面

基于 Laravel 10.x、Vue 3 和 Nuxt UI 开发的 SQL 查询页面。

## 运行项目

安装后端和前端依赖：

```bash
composer install
npm install
```

复制环境配置文件：

打开 `.env`，填写数据库配置：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_sql_edit
DB_USERNAME=root
DB_PASSWORD=你的数据库密码
```

创建对应的空数据库，然后执行：

```bash
php artisan key:generate
php artisan migrate --seed
```

启动 Laravel：

```bash
php artisan serve
```

再打开一个终端启动前端开发服务：

```bash
npm run dev
```

根据终端提示访问项目，默认 Laravel 地址通常为 <http://127.0.0.1:8000>。

## 默认登录信息

```text
邮箱：test@email.com
密码：12345678
```
