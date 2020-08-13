<?php

namespace ArtemiyKudin\Bonus\Controllers;

use ArtemiyKudin\Bonus\Traits\MarketBonusService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MarketApiController extends ApiController
{
    use MarketBonusService;

    public function bonus(Request $request): object
    {
        $user = $this->currentUser;

        if (!$user) {
            throw new ModelNotFoundException(config('marketBonus.errors.user_not_found'), Response::HTTP_FORBIDDEN);
        }

        $arrBonus['bonuses'] = $this->userBonuses($user->userID);
        $arrBonus['percent'] = $this->userDiscount($user);
        $arrBonus['list'] = $this->listOfOperations($this->currentUser, $request->skip);

        return $this->json($arrBonus, self::HTTP_OK);
    }
}
