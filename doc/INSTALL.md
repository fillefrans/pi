1. Install Ubuntu/Debian with LAMP + SSH server


2. Install tools

sudo apt-get install git php5-dev


git clone https://github.com/igbinary/igbinary.git
cd igbinary
phpize
./configure CFLAGS="-O2 -g" --enable-igbinary
make && sudo make install



3. Install Redis

wget http://download.redis.io/releases/redis-2.8.13.tar.gz
tar xzf redis-2.8.13.tar.gz
cd redis-2.8.13

sudo make install

sudo mkdir /etc/redis
sudo mkdir /var/redis
sudo mkdir /var/data
sudo mkdir /var/data/redis


Copy init script "redis-server" to /etc/init.d/
Copy config file "redis.conf" to etc/redis/redis.conf



4. Install phpredis

git clone https://github.com/nicolasff/phpredis.git
cd phpredis
phpize
./configure --enable-redis-igbinary
make && sudo make install



5. Install Varnish

sudo apt-get install libedit-dev libncurses5-dev libpcre3-dev pkg-config make

wget http://repo.varnish-cache.org/source/varnish-3.0.5.tar.gz
tar xvfz varnish-3.0.5.tar.gz
cd varnish-3.0.5
./configure
make && sudo make install




