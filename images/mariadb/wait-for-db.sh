#!/bin/bash

# Wait for init database

FILEDB="/var/lib/mysql/redsoft/db.opt"

maxcounter=20
counter=1

while [ ! -f $FILEDB ]; do
    >&2 echo "Wait until the database is initialized"
    sleep 5

    counter=`expr $counter + 1`
        if [ $counter -gt $maxcounter ]; then
            >&2 echo "We have been waiting for MySQL too long already; failing."
            exit 1
        fi;
done

exit 0
