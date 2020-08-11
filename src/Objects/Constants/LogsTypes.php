<?php

namespace ArtemiyKudin\Bonus\Objects\Constants;

use ArtemiyKudin\Bonus\Objects\Interfaces\LogsTypesInterface;

class LogsTypes implements LogsTypesInterface
{
    //Types
    private const TYPE_CLIENT_IMPORT = 1;
    private const TYPE_LOGIN = 2;
    private const TYPE_LOGOUT = 3;

    public function importType(): int
    {
        return self::TYPE_CLIENT_IMPORT;
    }

    public function loginType(): int
    {
        return self::TYPE_LOGIN;
    }

    public function logoutType(): int
    {
        return self::TYPE_LOGOUT;
    }

    public function typeName(int $id): string
    {
        return $this->typeArray()[$id];
    }

    public function typeArray(): array
    {
        return [
            self::TYPE_CLIENT_IMPORT => __('logs.importing_clients'),
            self::TYPE_LOGIN => __('logs.employee_login'),
            self::TYPE_LOGOUT => __('logs.employee_exit')
        ];
    }
}
