gomexsi - TAMUCC..
=======

## Running tests

In order to make sure that our backend php connector is working as expecting, we're using travis-cli.org and phpunit.

The file "backend/HelloWorld.php" is currently an example of a file under test, with unit tests sitting in Tests/ folder.  To run test on the commandline execute ```phpunit --configuration=phpunit_pgsql.xml```. This assumes that local postgres instance exists with user [postgres] with password [] (empty).  

In addition to being able to run this on the commandline, travis-cli.org also runs the tests on each and every git commit.

Example output:
```
jorrit$ phpunit --configuration phpunit_pgsql.xml 
PHPUnit 3.7.14 by Sebastian Bergmann.

Configuration read from /Volumes/Data/Users/jorrit/dev/gomexsi/phpunit_pgsql.xml

....

Time: 0 seconds, Memory: 5.75Mb

OK (4 tests, 5 assertions)
```

