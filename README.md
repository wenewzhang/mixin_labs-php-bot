# mixin_labs-php-bot
Php bot for Mixin Network

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
//install composer to /usr/local/opt/php@7.2/bin and give a brief name 'composer'
php composer-setup.php --install-dir=/usr/local/opt/php@7.2/bin --filename=composer
php -r "unlink('composer-setup.php');"

```
# 安装依赖包
```bash
composer install
```
### 测试
```bash
php chat-client.php
```

test:
../../vendor/bin/phpunit MessageApiTest.php
