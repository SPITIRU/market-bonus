<?php

namespace ArtemiyKudin\Bonus\Traits;

use ArtemiyKudin\Bonus\Models\MarketBonusSetting;
use Beauty\Modules\Common\Models\User;
use Beauty\Modules\Common\Objects\MymarketEntity\Order\Constants\OrderArrayStatus;
use Beauty\Modules\Common\Repositories\MarketOrderRepository;
use Beauty\Modules\Common\Repositories\MarketStepPercentRepository;
use Carbon\Carbon;

trait MarketBonusService
{
    public function userBonuses(int $userID): int
    {
        $orders = $this->ordersUserForDiscount($userID);

        if (!$this->checkArrayForFullness($orders)) {
            return 0;
        }

        $listDeductBonuses = $this->listDeductBonuses($userID);

        $addBonuses = isset($orders) ? $orders->sum('addBonuses') : 0;
        $deductBonuses = isset($listDeductBonuses) ? $listDeductBonuses->sum('deductBonuses') : 0;

        return $addBonuses - $deductBonuses;
    }

    public function userDiscount(object $user): int
    {
//        $marketOrderRepository = config('marketBonus.resolve.marketOrdersRepo');
        $status = new OrderArrayStatus(null);
        $cancelled = $status->cancelled();

        $orders = $user->marketOrders()->where('status', '<>', $cancelled)->get();

        $sum = 0;
        if ($this->checkArrayForFullness($orders)) {
            foreach ($orders as $order) {
                $sum += resolve(MarketOrderRepository::class)->getOrderTotalPrice($order);
            }
        }

        $marketStepsPercent = $this->discount($sum);
        return isset($marketStepsPercent) ? $marketStepsPercent->percent : 0;
    }

    public function listOfOperations(User $user, ?int $skip): array
    {
        $orders = $this->listUnallocatedBonuses($user, $skip);

        if ($orders['count'] > 0) {
            return $this->historyOrders($orders);
        }

        return [
            'count' => 0,
            'orders' => null
        ];
    }

    public function listUnallocatedBonuses(User $user, ?int $skip): ?array
    {
        $status = new OrderArrayStatus(null);
        $cancelled = $status->cancelled();
        $date = $this->minDateBonuses();
        $marketOrders = $user->marketOrders()
            ->when(isset($date), function ($q) use ($date) {
                return  $q->where('dateBonuses', '>=', $date);
            })
            ->where('status', '<>', $cancelled)
            ->orderBy('dateBonuses');
        $count = $marketOrders->count();
        $orders = $marketOrders->skip($skip)->take($this->settings['take_fifteen'])->get();

        return [
            'count' => $count,
            'orders' => $orders
        ];
    }

    private function historyOrders(array $orders): array
    {
        $arrOrders = [];

        foreach ($orders['orders'] as $order) {
            $arrOrders[] = [
                'date' => Carbon::parse($order->dateBonuses)->format('d.m.Y'),
                'name' => $this->nameOperation($order),
                'price' => $this->priceOperation($order)
            ];
        }
        return [
            'count' => $orders['count'],
            'orders' => $arrOrders
        ];
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

    private function ordersUserForDiscount(int $userID): ?object
    {
        $status = new OrderArrayStatus(null);
        $date = $this->minDateBonuses();

//        dd(app(config('marketBonus.models.marketOrders')));
        return config('marketBonus.resolve.marketOrders')
            ->where('userID', $userID)
            ->where('status', $status->shipped())
            ->when(isset($date), function ($q) use ($date) {
                return  $q->where('dateBonuses', '>=', $date);
            })
            ->get();
    }

    private function listDeductBonuses(int $userID): ?object
    {
        $status = new OrderArrayStatus(null);
        $date = $this->minDateBonuses();
        return config('marketBonus.resolve.marketOrders')
            ->where('userID', $userID)
            ->where('status', '<>', $status->cancelled())
            ->when(isset($date), function ($q) use ($date) {
                return  $q->where('dateBonuses', '>=', $date);
            })
            ->whereNotNull('deductBonuses')
            ->get();
    }

    private function discount(int $userBonuses): ?object
    {
        $marketStepPercentRep = resolve(MarketStepPercentRepository::class);
        $arrStepPercent = $marketStepPercentRep->arrStepPercent();

        if (!checkArrayForFullness($arrStepPercent)) {
            return null;
        }

        $len = count($arrStepPercent);
        foreach ($arrStepPercent as $i => $step) {
            if ($step >= $userBonuses) {
                $value = $step;
                break;
            }

            if ($i === $len - 1) {
                $value = $step;
                break;
            }
        }

        return $marketStepPercentRep->percentByAmount($value);
    }

    private function nameOperation($order): string
    {
        return (!$order->deductBonuses ? __('market.enrollment') : __('market.writeDowns')) . $order->orderID;
    }

    private function priceOperation($order): int
    {
        return intval($order->deductBonuses ? - $order->deductBonuses : $order->addBonuses);
    }
}
