gomexsi - TAMUCC..
=======

[![Build Status](https://travis-ci.org/jhpoelen/gomexsi.png)](https://travis-ci.org/jhpoelen/gomexsi)

## Sequence Diagram

[![Request handling](http://www.websequencediagrams.com/cgi-bin/cdraw?lz=dGl0bGUgaW52b2tpbmcgYSB0cm9waGljIHNlcnZpY2UKClVJLT5SZXF1ZXN0SGFuZGxlcjogaHR0cCBwb3N0IHIAFAYKABQOACAScGFyc2UAQAcAHBFGYWN0b3J5OiBjcmVhdGVTAHQHABAHLT4ACgcAGAgKABoHAIEGEgB2EQAxCWZpbmRQcmV5Rm9yUHJlZGF0b3IAgRMhAIEIBlJlc3BvbnNlAIFSEVVJAIF7DgAgBwo&s=napkin)](http://www.websequencediagrams.com/cgi-bin/cdraw?lz=dGl0bGUgaW52b2tpbmcgYSB0cm9waGljIHNlcnZpY2UKClVJLT5SZXF1ZXN0SGFuZGxlcjogaHR0cCBwb3N0IHIAFAYKABQOACAScGFyc2UAQAcAHBFGYWN0b3J5OiBjcmVhdGVTAHQHABAHLT4ACgcAGAgKABoHAIEGEgB2EQAxCWZpbmRQcmV5Rm9yUHJlZGF0b3IAgRMhAIEIBlJlc3BvbnNlAIFSEVVJAIF7DgAgBwo&s=napkin) 

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

