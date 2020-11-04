<?php

namespace App\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordBrokerManager as BasePasswordBrokerManager;
use Illuminate\Contracts\Auth\PasswordBrokerFactory as FactoryContract;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PasswordBrokerManager extends BasePasswordBrokerManager implements FactoryContract
{
    /**
     * Resolve the given broker.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        // The password broker uses a token repository to validate tokens and send user
        // password e-mails, as well as validating that password reset process as an
        // aggregate service of sorts providing a convenient interface for resets.
        return new PasswordBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'] ?? null)
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }
}
