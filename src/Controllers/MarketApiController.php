<?php

namespace ArtemiyKudin\Bonus\Controllers;

use ArtemiyKudin\Bonus\Traits\MarketBonusService;
use Illuminate\Http\Request;

class MarketApiController extends ApiController
{
    use MarketBonusService;

    public function bonus(Request $request): object
    {
        $user = $this->currentUser;
        $arrBonus['bonuses'] = $this->userBonuses($user->userID);
        $arrBonus['percent'] = $this->userDiscount($user);
        $arrBonus['list'] = $this->listOfOperations($this->currentUser, $request->skip);

        return $this->json($arrBonus, self::HTTP_OK);
    }
}
