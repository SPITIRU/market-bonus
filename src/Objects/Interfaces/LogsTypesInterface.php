<?php

namespace ArtemiyKudin\Bonus\Objects\Interfaces;

interface LogsTypesInterface
{
    public function importType(): int;
    public function loginType(): int;
    public function logoutType(): int;

    public function typeName(int $id): string;
    public function typeArray(): array;
}
