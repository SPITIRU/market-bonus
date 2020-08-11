<?php

namespace ArtemiyKudin\Bonus\Models;

use ArtemiyKudin\Bonus\Traits\HasLogs;
use Illuminate\Database\Eloquent\Model;

class MarketStepsPercent extends Model
{
    use HasLogs;

    protected $table = 'market_steps_percent';

    protected $primaryKey = 'stepPercentID';

    protected $guarded = ['_token'];

    public function updateStepsPercent($data): void
    {
        if (isset($data->stepPercent)) {
            $modelIDs = [];
            foreach ($data->stepPercent as $key => $step) {
                if (strpos($key, 'new') !== false) {
                    $model = $this->model->create(['amount' => $step['amount'], 'percent' => $step['percent']]);
                    $modelIDs[] = $model->stepPercentID;
                } else {
                    $model = $this->model->find($key);
                    $model->amount = $step['amount'];
                    $model->percent = $step['percent'];
                    $model->update();

                    $modelIDs[] = $model->stepPercentID;
                }
            }

            if (checkArrayForFullness($modelIDs)) {
                $deleteModels = $this->model->whereNotIn('stepPercentID', $modelIDs)->get();
                if (checkArrayForFullness($deleteModels)) {
                    foreach ($deleteModels as $deleteModel) {
                        $deleteModel->delete();
                    }
                }
            }
        } else {
            $this->model->truncate();
        }
    }

    public function arrStepPercent(): ?array
    {
        return $this->bonusActive ? $this->model->orderBy('amount')->pluck('amount')->toArray() : null;
    }

    public function percentByAmount($value): object
    {
        return $this->model->where('amount', $value)->first();
    }
}
