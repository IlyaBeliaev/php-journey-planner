PHP Journey Planner
===================

Reference implementation of the connection scan algorithm in PHP. Currently uses UK Rail data but could include any GTFS-ish dataset.

```
composer install
./vendor/bin/phpunit
```

# TODO

- Connections after midnight
- Non timetable connection time windows
- Fix calendar end date issues
- Geographical connection pruning
