<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Controller;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Routing\RouteMatchInterface;
use Retrofit\Drupal\Form\ArrayAccessFormState;
use Retrofit\Drupal\Form\DrupalGetForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class DrupalGetFormController implements ContainerInjectionInterface
{
    public function __construct(
        private readonly ClassResolverInterface $classResolver,
        private readonly FormBuilderInterface $formBuilder,
        private readonly ModuleHandlerInterface $moduleHandler
    ) {
    }

    public static function create(ContainerInterface $container)
    {
        return new self(
            $container->get('class_resolver'),
            $container->get('form_builder'),
            $container->get('module_handler')
        );
    }

    public function getForm(RouteMatchInterface $routeMatch)
    {
        $route = $routeMatch->getRouteObject();
        assert($route !== null);
        if ($route->hasOption('file')) {
            $modulePath = $this->moduleHandler->getModule(
                $route->getOption('module')
            )->getPath();
            $includePath = $modulePath . '/' . $route->getOption('file');
            if (file_exists($includePath)) {
                require_once $includePath;
            }
        }
        $form_object = $this->classResolver->getInstanceFromDefinition(
            DrupalGetForm::class
        );
        $form_object->setFormId($route->getDefault('_form_id'));
        $form_state = new ArrayAccessFormState(new FormState());
        $args = $routeMatch->getRawParameters()->all();
        $form_state->addBuildInfo('args', array_values($args));
        return $this->formBuilder->buildForm($form_object, $form_state);
    }
}
