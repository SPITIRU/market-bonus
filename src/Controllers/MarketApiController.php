<?php

namespace ArtemiyKudin\Bonus\Controllers;

use ArtemiyKudin\Bonus\Traits\MarketBonusService;
use Illuminate\Http\Request;

class MarketApiController extends ApiController
{
    use MarketBonusService;

    public function bonus(Request $request): object
    {
        $arrBonus['bonuses'] = $this->userBonuses($this->currentUser);
        $arrBonus['percent'] = $this->userDiscount($this->currentUser);
        $arrBonus['list'] = $this->listOfOperations($this->currentUser, $request->skip);

        return $this->json($arrBonus, self::HTTP_OK);
    }
}
