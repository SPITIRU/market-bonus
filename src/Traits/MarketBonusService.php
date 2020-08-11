<?php

namespace ArtemiyKudin\Bonus\Traits;

use ArtemiyKudin\Bonus\Models\MarketBonusSetting;
use Beauty\Modules\Common\Objects\MymarketEntity\Order\Constants\OrderArrayStatus;
use Carbon\Carbon;

trait MarketBonusService
{
    public function userBonuses(object $user): int
    {
        $orders = $this->ordersUserForDiscount($user);

        if (!$this->checkArrayForFullness($orders)) {
            return 0;
        }

        $listDeductBonuses = $this->listDeductBonuses($user);

        $addBonuses = $orders->sum('addBonuses');
        $deductBonuses = $listDeductBonuses->sum('deductBonuses');

        return $addBonuses - $deductBonuses;
    }

    public function userDiscount(object $user): int
    {
        $marketOrderRepository = resolve(MarketOrderRepository::class);
        $status = new OrderArrayStatus(null);
        $cancelled = $status->cancelled();

        $orders = $user->marketOrders()->where('status', '<>', $cancelled)->get();

        $sum = 0;
        if ($this->checkArrayForFullness($orders)) {
            foreach ($orders as $order) {
                $sum += $marketOrderRepository->getOrderTotalPrice($order);
            }
        }

        $marketStepsPercent = resolve(MarketStepPercentRepository::class)->discount($sum);
        return isset($marketStepsPercent) ? $marketStepsPercent->percent : 0;
    }

    public function listOfOperations(object $user, $skip): array
    {
        $status = new OrderArrayStatus(null);
        $cancelled = $status->cancelled();

        $orders = $user->marketOrders()
            ->where('status', '<>', $cancelled)
            ->orderBy('dateBonuses')
            ->skip($skip)
            ->take($this->settings['take_fifteen'])
            ->get();
    }

    public function checkArrayForFullness($arrayOrObject, int $count = 0, bool $equality = false): bool
    {
        if (isset($arrayOrObject)) {
            if ($equality && count($arrayOrObject) == $count) {
                return true;
            }

            if (!$equality && count($arrayOrObject) > $count) {
                return true;
            }
        }

        return false;
    }

    public function minDateBonuses(): ?string
    {
        if (!resolve(MarketBonusSetting::class)->active()) {
            return null;
        }

        $days = resolve(MarketBonusSetting::class)->days();
        return Carbon::now()->subDays($days)->format('Y-m-d H:i:s');
    }

    private function ordersUserForDiscount(object $user): ?object
    {
        $status = new OrderArrayStatus(null);

        $date = $this->minDateBonuses();
//        dd(app(config('marketBonus.models.marketOrders')));
        return config('marketBonus.resolve_models.marketOrders')
            ->where('userID', $user->userID)
            ->where('status', $status->shipped())
            ->when(isset($date), function ($q) use ($date) {
                return  $q->where('dateBonuses', '>=', $date);
            })
            ->get();
    }
}
