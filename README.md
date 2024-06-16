GatewayClient
=================
`composer install`

`composer require workerman/gatewayclient`

```php
use GatewayClient\Gateway;

Gateway::$registerAddress = '127.0.0.1:4001';

Gateway::bindUid($client_id, $uid);
Gateway::joinGroup($client_id, $group_id);

Gateway::sendToUid($uid, $message);
Gateway::sendToGroup($group, $message);

Gateway::sendToAll($data);
Gateway::sendToClient($client_id, $data);
Gateway::closeClient($client_id);
Gateway::isOnline($client_id);
Gateway::bindUid($client_id, $uid);
Gateway::isUidOnline($uid);
Gateway::getClientIdByUid($uid);
Gateway::unbindUid($client_id, $uid);
Gateway::sendToUid($uid, $data);
Gateway::joinGroup($client_id, $group);
Gateway::sendToGroup($group, $data);
Gateway::leaveGroup($client_id, $group);
Gateway::getClientCountByGroup($group);
Gateway::getClientSessionsByGroup($group);
Gateway::getAllClientCount();
Gateway::getAllClientSessions();
Gateway::setSession($client_id, $session);
Gateway::updateSession($client_id, $session);
Gateway::getSession($client_id);
```