#!/bin/bash
# transfer GIGpress data from ShopDB
user=RandomNoizeShop
pw=shitthat
#user=shopdb;
#pw=shopdb;
files="transferArtists.sql transferVenues.sql transferShows.sql transferNews.sql"   

tables="dates news manufacturers countries"

for table in $tables
do
	mysqldump -c -uRandomNoizeShop -pshitthat ShopDB $table | mysql -uwordpress -pwordpress wordpress
done

