#!/bin/bash

# Symfony application
cd /vagrant

# Install vendors
composer.phar install

php bin/console doc:dat:create
php bin/console doc:sche:up --force
