[GoMexSI at Texas A&M Corpus Christi](http://gomexsi.tamucc.edu/) 
=======

[![Build Status](https://travis-ci.org/GoMexSI/gomexsi-web.png)](https://travis-ci.org/GoMexSI/gomexsi-web)

#### Sequence Diagram

[![Request handling](http://www.websequencediagrams.com/cgi-bin/cdraw?lz=dGl0bGUgaW52b2tpbmcgYSB0cm9waGljIHNlcnZpY2UKClVJLT5SZXF1ZXN0SGFuZGxlcjogaHR0cCBwb3N0IHIAFAYKABQOACAScGFyc2UAQAcAHBFGYWN0b3J5OiBjcmVhdGVTAHQHABAHLT4ACgcAGAgKABoHAIEGEgB2EQAxCWZpbmRQcmV5Rm9yUHJlZGF0b3IAgRMhAIEIBlJlc3BvbnNlAIFSEVVJAIF7DgAgBwo&s=napkin)](http://www.websequencediagrams.com/?lz=dGl0bGUgaW52b2tpbmcgYSB0cm9waGljIHNlcnZpY2UKClVJLT5SZXF1ZXN0SGFuZGxlcjogaHR0cCBwb3N0IHIAFAYKABQOACAScGFyc2UAQAcAHBFGYWN0b3J5OiBjcmVhdGVTAHQHABAHLT4ACgcAGAgKABoHAIEGEgB2EQAxCWZpbmRQcmV5Rm9yUHJlZGF0b3IAgRMhAIEIBlJlc3BvbnNlAIFSEVVJAIF7DgAgBwo&s=napkin)

## Running tests

In order to make sure that our backend php connector is working as expecting, we're using travis-cli.org and phpunit.

Unit tests live in the Tests/ directory.  To run test on the commandline execute ```phpunit``` in the gomexsi root folder.  

In addition to being able to run this on the commandline, travis-cli.org also runs the tests on each and every git commit.

Example output:
```
jorrit$ phpunit 
Configuration read from /Volumes/Data/Users/jorrit/dev/gomexsi/phpunit.xml

.......................

Time: 10 seconds, Memory: 7.00Mb

OK (23 tests, 85 assertions)
```

