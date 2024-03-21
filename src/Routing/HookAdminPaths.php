<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Routing;

use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\Core\State\StateInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class HookAdminPaths implements EventSubscriberInterface
{
    /**
     * @var array{admin: string, non_admin: string}
     */
    private array $paths = [
        'admin' => '',
        'non_admin' => '',
    ];

    public function __construct(
        private readonly StateInterface $state
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Try to set a low priority to ensure that all routes are already added.
            RoutingEvents::ALTER => ['calculate', -1024],
            RoutingEvents::FINISHED => 'store',
        ];
    }

    public function calculate(RouteBuildEvent $event): void
    {
        // @todo support `hook_admin_paths_alter` with the routes marked
        //   and adjust the option if changed or added.
        $collection = $event->getRouteCollection();

        $admin = [];
        $non_admin = [];
        foreach ($collection->all() as $route) {
            if (!$route->hasOption('_admin_route')) {
                continue;
            }
            $enabled = $route->getOption('_admin_route');
            if ($enabled) {
                $admin[] = $route->getPath();
            } else {
                $non_admin[] = $route->getPath();
            }
        }

        $this->paths = [
            'admin' => implode("\n", $admin),
            'non_admin' => implode("\n", $non_admin),
        ];
    }

    public function store(): void
    {
        $this->state->set('router.admin_paths', $this->paths);
        $this->paths['admin'] = '';
        $this->paths['non_admin'] = '';
    }

    /**
     * @return array{admin: string, non_admin: string}
     */
    public function get(): array
    {
        /** @var array{admin: string, non_admin: string} $paths */
        $paths = $this->state->get('router.admin_paths', ['admin' => '', 'non_admin' => '']);
        return $paths;
    }
}
