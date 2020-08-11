<?php

namespace ArtemiyKudin\Bonus\Controllers;

use Beauty\Modules\Common\Objects\Profile\Profile as Profiles;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Controller as CommonController;

class ApiController extends CommonController
{
    protected $json;
    protected $token;
    protected $user;
    protected $settings;
    protected $currentUser;
    protected $profile;

    /**
     * HTTP status codes
     */
    protected const HTTP_OK = 200;
    protected const HTTP_CREATED = 201;
    protected const HTTP_NO_DATA = 204;
    protected const HTTP_BAD_REQUEST = 400;
    protected const HTTP_UNAUTHORIZED_ERROR = 401;
    protected const HTTP_FORBIDDEN = 403;
    protected const HTTP_NOT_FOUND = 404;
    protected const HTTP_UNPROCESSABLE_ENTITY = 422;
    protected const HTTP_SERVER_ERROR = 500;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->currentUser = auth('api')->user();

            if (isset($this->currentUser)) {
                $profile = new Profiles($this->currentUser);
                $this->profile = $profile->profile();
            }

            return $next($request);
        });

        $this->settings = Config::get('settings');
    }

    /**
     * @param array $data
     * @param int|null $status
     * @param array|null $headers
     * @return object
     */
    protected function json($data = [], ?int $status = self::HTTP_OK, ?array $headers = []): object
    {
        return response()->json($this->filterData($data), $status, $headers);
    }

    /**
     * Функция убирает null и удаляет лишние связи в запросах
     * @param $data
     * @return array|null
     */
    private function filterData($data): ?array
    {
        $data = collect($data)->toArray();

        // Удаляем ключи со значением null
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = $this->filterData($v);
            }
            if ($v === null) {
                $data[$k] = '';
            }
        }

        return $data;
    }
}
