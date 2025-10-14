<?php

namespace App\Services;

use App\Exceptions\BaseException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\ExceptionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class UserService extends Service
{
    use ExceptionTrait;

    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * @param array $data
     * @return array|JsonResponse
     * @throws BaseException
     */
    public function createUser(array $data): array|JsonResponse
    {
        if (empty($data['mobile'])) {
            $this->throwValidation('');
        }

        $data['username'] = $data['mobile'];
        $data['password'] = bcrypt($data['mobile']);

        return $this->response->success('', $this->userRepository->findOrCreate($data)->toArray());
    }
}
