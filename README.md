PHP Journey Planner
===================

Reference implementation of the connection scan algorithm in PHP. Currently uses UK Rail data but could include any GTFS-ish dataset.

```
cd server/
composer install
./vendor/bin/phpunit
./bin/import-data
# cup of tea and a twix
./bin/run TON CHX
```

# Known issues

- Connections that start after midnight are not currently considered
- Non timetable connection time windows are not applied
- Interchange is being applied at stations where there is an assocatiation (join/split)
- Journeys that start with a transfer won't work in the SchedulePlanner
