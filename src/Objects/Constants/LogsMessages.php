<?php

namespace ArtemiyKudin\Bonus\Objects\Constants;

use ArtemiyKudin\Bonus\Objects\Interfaces\LogsMessagesInterface;

class LogsMessages implements LogsMessagesInterface
{
    //Messages
    private const CLIENT_LOGIN_LOG = 1;
    private const CLIENT_IMPORT_LOG = 2;

    public function clientLoginLog(): int
    {
        return self::CLIENT_LOGIN_LOG;
    }

    public function clientImportLog(): int
    {
        return self::CLIENT_IMPORT_LOG;
    }

    public function messageName(int $id): ?string
    {
        return $this->messageArray()[$id];
    }

    public function messageArray(): array
    {
        return [
            self::CLIENT_LOGIN_LOG => __('logs.save_client_login_log'),
            self::CLIENT_IMPORT_LOG => __('logs.save_client_import_log'),
        ];
    }
}
