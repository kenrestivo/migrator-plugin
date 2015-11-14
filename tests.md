# Tests

There's no test suite yet. However these are the quick-n-dirty examples:

```sh
curl -i -u testes:test -i http://hubzilla/migrator/version
curl -i -u testes:test -i http://hubzilla/migrator/export/users
curl -i -u testes:test -i http://hubzilla/migrator/export/channel_hashes/${ACCOUNT_ID}
curl -i -u testes:test http://hubzilla/migrator/export/identity/${SOME_LONG_CHAN_ID}

``
