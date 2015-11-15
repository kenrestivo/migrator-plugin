# Tests

There's no test suite yet. However these are the quick-n-dirty examples:

```sh
curl -u testes:test -i http://hubzilla/migrator/version
curl -u testes:test -i http://hubzilla/migrator/export/users
curl -u testes:test -i http://hubzilla/migrator/export/channel_hashes/${ACCOUNT_ID}
curl -u testes:test -i http://hubzilla/migrator/export/identity/${SOME_LONG_CHAN_ID}

``

These should authfail

```sh
curl -i http://hubzilla/migrator/version
curl -i http://hubzilla/migrator
curl -i http://hubzilla/migrator/export/users

```
