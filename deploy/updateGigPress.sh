#!/bin/bash
# transfer GIGpress data from ShopDB
user=wordpress
pw=wordpress
#user=shopdb;
#pw=shopdb;
files="transferArtists.sql transferVenues.sql transferShows.sql transferNews.sql"   

# transfer tables
sh transferTablesFromShopDB.sh

# extract the data
for file in $files
do
	stat -t $file;
	if [ $? = 0 ]; 
	then 
		echo mysql -v -uwordpress -pwordpress < $file;
#		read -p 'press enter to execute this';
		mysql -uwordpress -pwordpress wordpress < $file;
	else
		echo $file not found;
	fi;
done;
