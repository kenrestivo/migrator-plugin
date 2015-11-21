# Tests

There's no test suite yet. However these are the quick-n-dirty examples:

```sh
curl -u testes:test -i http://hubzilla/migrator/version
curl -u testes:test -i http://hubzilla/migrator/export/users
curl -u testes:test -i http://hubzilla/migrator/export/channel_hashes/${ACCOUNT_ID}
curl -u testes:test -i http://hubzilla/migrator/export/identity/${SOME_LONG_CHAN_ID}
curl -u testes:test -i http://hubzilla/migrator/export/first_post/${SOME_LONG_CHAN_ID}
curl -u testes:test -i http://hubzilla/migrator/export/items/${SOME_LONG_CHAN_ID}/${SOME_YEAR}/${SOME_MONTH}

curl -u testes:test -H "Content-Type: application/json" --data @sample-account.json http://hubzilla/migrator/import/account


```

These should authfail

```sh
curl -i http://hubzilla/migrator/version
curl -i http://hubzilla/migrator
curl -i http://hubzilla/migrator/export/users

```

Redmatrix tests

```sh
curl -u testes:test -i http://redmatrix/migrator/version
curl -u testes:test -i http://redmatrix/migrator/export/users
curl -u testes:test -i http://redmatrix/migrator/export/channel_hashes/${ACCOUNT_ID}
curl -u testes:test -i http://redmatrix/migrator/export/identity/${SOME_LONG_CHAN_ID}
curl -u testes:test -i http://redmatrix/migrator/export/first_post/${SOME_LONG_CHAN_ID}
curl -u testes:test -i http://redmatrix/migrator/export/items/${SOME_LONG_CHAN_ID}/${SOME_YEAR}/${SOME_MONTH}

curl -u testes:test -H "Content-Type: application/json" --data @sample-account.json http://redmatrix/migrator/import/account


```
