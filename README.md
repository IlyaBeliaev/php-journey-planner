PHP Journey Planner
===================

Reference implementation of the connection scan algorithm in PHP. Currently uses UK Rail data but could include any GTFS-ish dataset.

## CLI

```
cd server/
composer install
./vendor/bin/phpunit
./bin/import-data
# cup of tea and a twix (~30 mins)
./bin/find-transfer-patterns
# quick pint down the local (~3 hours)
./bin/run TON CHX
```

## Web interface

```
vagrant up
vagrant ssh
/var/www/api-ttt.local/bin/import-data
# chai latte and a gluten free bagel (~30 mins)
/var/www/api-ttt.local/bin/find-transfer-patterns
# game of hacky sack down the local park (~3 hours)
cd /var/www/ttt.local/
npm run deploy
```

Open your web browser and go to `http://ttt.local`


# Known issues

- Connections that start after midnight are not currently considered
- Interchange is being applied at stations where there is an assocatiation (join/split)
- Connection Scan Algorithm is geared towards the fastest journeys, might be better way of getting more transfer patterns
- Transfer patterns should be marked as fastest, cheapest, least changes etc

