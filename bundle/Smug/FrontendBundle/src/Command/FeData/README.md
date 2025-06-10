Build
==============

yarn enSmug\Core dev
yarn enSmug\Core dev --watch

Docker
==============
##Rebuild
`     docker-compose stop && docker-compose rm php && docker-compose build --no-cache --pull php
` #IP von Cointainer anzeigen
    docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' sw6_app_server_1
    
    docker inspect <container id> | grep "IPAddress"
    docker inspect 51d03239b8a3 | grep "IPAddress"

Unit tests
==============
vendor/bin/phpunit ./tests/workPlay/MarketingBundle/Services/Campaign/Listing/ListServiceTest.php

Split Import XML
==============
xml_split -s 20Mb symfony/src/Command/Vlb/data/Initial/data/Onix30VollExp_036.xml 

Import SQL on Server
==============
mysql -u h202795_storysh -p h202795_storysh < storysh.sql 

Bitbucket
MkKRx9q4ctyNpCPsHEH9

Server NVM use
export NVM_DIR="$([ -z "${XDG_CONFIG_HOME-}" ] && printf %s "${HOME}/.nvm" || printf %s "${XDG_CONFIG_HOME}/nvm")"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"


Import Reihenfolge
==============
- sudo bash ./import.sh
- php -d memory_limit=-1 bin/console image:fe:optimization
- php -d memory_limit=-1 bin/console publication:fe:data
- php -d memory_limit=-1 bin/console market:fe:data
- php -d memory_limit=-1 bin/console author:fe:data
- php -d memory_limit=-1 bin/console genre:fe:data
- rsync -avP symfony/public/Resources/Data/Genre/ h202795@web215.dogado.net:/var/www/vhosts/h202795.web215.dogado.net/storysh.de/public/Resources/Data/Genre
- rsync -avP symfony/public/Resources/Data/Publication/ h202795@web215.dogado.net:/var/www/vhosts/h202795.web215.dogado.net/storysh.de/public/Resources/Data/Publication
- rsync -avP symfony/public/Resources/Data/Market/ h202795@web215.dogado.net:/var/www/vhosts/h202795.web215.dogado.net/storysh.de/public/Resources/Data/Market
- rsync -avP symfony/public/Resources/Data/Story/ h202795@web215.dogado.net:/var/www/vhosts/h202795.web215.dogado.net/storysh.de/public/Resources/Data/Story
- rsync -avP symfony/public/Resources/Data/Author/ h202795@web215.dogado.net:/var/www/vhosts/h202795.web215.dogado.net/storysh.de/public/Resources/Data/Author
- rsync -avP symfony/public/_uploads/images/media/publications/thumbnails/ h202795@web215.dogado.net:/var/www/vhosts/h202795.web215.dogado.net/storysh.de/public/_uploads/images/media/publications/thumbnails
- rsync -avP symfony/public/_uploads/images/media/genres/thumbnails/ h202795@web215.dogado.net:/var/www/vhosts/h202795.web215.dogado.net/storysh.de/public/_uploads/images/media/genres/thumbnails