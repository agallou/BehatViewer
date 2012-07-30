#!/bin/sh

# Mandatory packages
sudo apt-get -y --force-yes install python-software-properties pwgen vim git make

# Mandatory repositories
grep "deb http://packages.dotdeb.org squeeze all" /etc/apt/sources.list
if [ $? != 0 ]
then
    echo "deb http://packages.dotdeb.org squeeze all" | sudo tee -a /etc/apt/sources.list
    echo "deb-src http://packages.dotdeb.org squeeze all" | sudo tee -a /etc/apt/sources.list
    wget -q -O - http://www.dotdeb.org/dotdeb.gpg | sudo apt-key add -
fi

sudo add-apt-repository -y ppa:gearman-developers/ppa
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 54E06A678456040BDCB76779AB759F011C73E014

sudo apt-add-repository ppa:flexiondotorg/java
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 2EA8F35793D8809A

grep "deb http://dl.google.com/linux/deb/ stable main" /etc/apt/sources.list
if [ $? != 0 ]
then
    echo "deb http://dl.google.com/linux/deb/ stable main" | sudo tee -a /etc/apt/sources.list
    wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
fi

# Updating APT
sudo apt-get update

# APT configuration
#MYSQL_PASS=$(pwgen -s 12 1);
MYSQL_PASS=""

cat <<MYSQL_PRESEED | debconf-set-selections
mysql-server-5.5 mysql-server/root_password password $MYSQL_PASS
mysql-server-5.5 mysql-server/root_password_again password $MYSQL_PASS
mysql-server-5.5 mysql-server/start_on_boot boolean true
MYSQL_PRESEED

# Install all required packages
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y --force-yes \
    gearman-job-server libgearman-dev gearman-tools libevent-dev uuid-dev \
    apache2 php5 php5-dev libapache2-mod-php5 php5-mysql php-pear php-apc php5-curl \
    sun-java6-jre sun-java6-plugin \
    xvfb firefox google-chrome-stable phantomjs \
    mysql-server

# Install PHP extensions
sudo pecl install gearman

echo "extension=gearman.so" | sudo tee /etc/php5/conf.d/gearman.ini

# Configure PHP
sudo sed -i 's/;date.timezone =/date.timezone = Europe\/Paris/g' /etc/php5/apache2/php.ini
sudo sed -i 's/;date.timezone =/date.timezone = Europe\/Paris/g' /etc/php5/cli/php.ini

# Restart services
sudo /etc/init.d/apache2 restart
Xvfb :99 -ac &