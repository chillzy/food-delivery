<?php

namespace App\Providers;

use App\Repositories\Admin\AdminRepository;
use App\Repositories\Admin\AdminRepositoryInterface;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\EmailVerification\EmailVerificationRepository;
use App\Repositories\EmailVerification\EmailVerificationRepositoryInterface;
use App\Repositories\Meal\MealRepository;
use App\Repositories\Meal\MealRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserAddress\UserAddressRepository;
use App\Repositories\UserAddress\UserAddressRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private const REPOSITORIES_BINDINGS = [
        UserRepositoryInterface::class => UserRepository::class,
        EmailVerificationRepositoryInterface::class => EmailVerificationRepository::class,
        UserAddressRepositoryInterface::class => UserAddressRepository::class,
        AdminRepositoryInterface::class => AdminRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        MealRepositoryInterface::class => MealRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        CartRepositoryInterface::class => CartRepository::class,
    ];

    public function register(): void
    {
        foreach (self::REPOSITORIES_BINDINGS as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }
}
