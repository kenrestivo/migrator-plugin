# Tests

There's no test suite yet. However these are the quick-n-dirty examples:

```sh
curl -u admin@you.com:test -i http://hubzilla/migrator/export/users
curl -u ken@spaz.org:test -i http://hubzilla/migrator/export/channels/${ACCOUNT_ID}
curl -i -u admin@you.com:test http://hubzilla/migrator/export/identity/${SOME_LONG_CHAN_ID}

``
