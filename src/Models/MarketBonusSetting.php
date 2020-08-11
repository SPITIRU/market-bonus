<?php

namespace ArtemiyKudin\Bonus\Models;

use Illuminate\Database\Eloquent\Model;

class MarketBonusSetting extends Model
{
    protected $table = 'market_bonus_settings';

    protected $primaryKey = 'bonusSettingID';

    protected $guarded = ['_token'];

    public function active(): ?bool
    {
        $model = $this->where('alias', 'active')->first();
        return $model ? $model->value : null;
    }

    public function days(): ?int
    {
        $model = $this->where('alias', 'durationsBonus')->first();
        return $model ? $model->value : null;
    }

    public function updateSettings($data): void
    {
        $bonus = $this->where('alias', 'bonus')->first();
        $durationsBonus = $this->where('alias', 'durationsBonus')->first();
        $active = $this->where('alias', 'active')->first();

        $bonus->value = $data->bonus;
        $bonus->update();

        $durationsBonus->value = $data->durationsBonus;
        $durationsBonus->update();

        $active->value = $data->active ? 1 : 0;
        $active->update();
    }
}
