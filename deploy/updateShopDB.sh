#!/bin/bash
# update DB script
user=RandomNoizeShop
pw=shitthat
#user=shopdb;
#pw=shopdb;
files=shopdb/mainAlumniArtistsUpdateLive.sql

for file in $files
do
	stat -t $file;
	if [ $? == 0 ] then 
		echo mysql -v -u$user -p$pw < $file;
		read -p 'press enter to execute this';
		mysql -v -u$user -p$pw < $file;
	else
		echo $file not found;
	fi;
done;
