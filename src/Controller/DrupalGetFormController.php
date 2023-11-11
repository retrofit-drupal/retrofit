<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Controller;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Retrofit\Drupal\Form\ArrayAccessFormState;
use Retrofit\Drupal\Form\DrupalGetForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DrupalGetFormController implements ContainerInjectionInterface
{
    public function __construct(
        private readonly ClassResolverInterface $classResolver,
        private readonly FormBuilderInterface $formBuilder,
        private readonly ModuleHandlerInterface $moduleHandler
    ) {
    }

    public static function create(ContainerInterface $container): self
    {
        return new self(
            $container->get('class_resolver'),
            $container->get('form_builder'),
            $container->get('module_handler')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getForm(RouteMatchInterface $routeMatch): array
    {
        $route = $routeMatch->getRouteObject();
        assert($route !== null);
        $formId = $route->getOption('form_id');
        if (!is_string($formId)) {
                throw new \InvalidArgumentException(
                    'The "form_id" option must be a string'
                );
        }
        if ($route->hasOption('file')) {
            $module = $route->getOption('module');
            if (!is_string($module)) {
                throw new \InvalidArgumentException(
                    'The "module" option must be a string'
                );
            }
            $modulePath = $this->moduleHandler->getModule($module)->getPath();
            $includePath = $modulePath . '/' . $route->getOption('file');
            if (file_exists($includePath)) {
                require_once $includePath;
            }
        }
        $form_object = $this->classResolver->getInstanceFromDefinition(
            DrupalGetForm::class
        );
        $form_object->setFormId($formId);
        $form_state = new ArrayAccessFormState();
        $args = $routeMatch->getRawParameters()->all();
        $form_state->addBuildInfo('args', array_values($args));
        return $this->formBuilder->buildForm($form_object, $form_state);
    }
}
