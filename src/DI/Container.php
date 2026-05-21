<?php

namespace App\DI;

use App\Config\Config;
use App\Model\Repository\ConversationRepository;
use App\Model\Repository\MessageRepository;
use App\Model\Repository\UserRepository;

final class Container
{
    // Výukový DI kontejner: drží konfiguraci, služby (singleton) a továrny na repository.
    private Config $config;
    private array $services;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->services = [];
    }

    public function getConfig(string $name): int|float|array|string|bool|null
    {
        return $this->config->get($name);
    }

    public function getService(string $name): mixed
    {
        if ($this->hasService($name)) {
            return $this->services[$name];
        }

        $servicesDefinitions = $this->getConfig('services') ?? [];

        if (!isset($servicesDefinitions[$name])) {
            throw new ServiceNotFoundException(
                'Service "' . $name . '" not found in services.yaml.'
            );
        }

        $definition = $servicesDefinitions[$name];
        $serviceClassname = is_string($definition)
            ? $definition
            : ($definition['class'] ?? null);

        if (!is_string($serviceClassname) || $serviceClassname === '') {
            throw new ServiceNotFoundException(
                'Service "' . $name . '" has invalid class definition in services.yaml.'
            );
        }

        $params = [];
        $definitionParams = is_array($definition) ? ($definition['params'] ?? []) : [];
        foreach ($definitionParams as $key => $value) {
            if (is_string($value) && str_starts_with($value, '@')) {
                // @database.dsn -> načti hodnotu z konfigurace
                $params[$key] = $this->getConfig(substr($value, 1));

                continue;
            }

            $params[$key] = $value;
        }
        $service = new $serviceClassname(...array_values($params));

        $this->services[$name] = $service;

        return $service;
    }

    public function hasService(string $name): bool
    {
        return isset($this->services[$name]);
    }

    public function createUserRepository(): UserRepository
    {
        return new UserRepository($this->getService('database'));
    }

    public function createConversationRepository(): ConversationRepository
    {
        return new ConversationRepository($this->getService('database'));
    }

    public function createMessageRepository(): MessageRepository
    {
        return new MessageRepository(
            $this->getService('database'),
            $this->createUserRepository(),
            $this->createConversationRepository(),
        );
    }

}
