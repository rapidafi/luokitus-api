### luokitus-api

Just some fairly simple PHP files that produce a GET only REST API for some selected codesets or classifications for which another project has made ready-to-read answers in its database. Original source for data is (currently just) [Opintopolku koodisto service](https://virkailija.opintopolku.fi/koodisto-service/swagger/index.html).

PHP scripts use [Predis](https://github.com/nrk/predis) ([v1.1.1](https://github.com/nrk/predis/releases/tag/v1.1.1)) which should be loaded as a subdirectory "predis".
