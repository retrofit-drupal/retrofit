<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Controller;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Retrofit\Drupal\Form\ArrayAccessFormState;
use Retrofit\Drupal\Form\DrupalGetForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class DrupalGetFormController implements ContainerInjectionInterface
{
    public function __construct(
        private readonly ClassResolverInterface $classResolver,
        private readonly FormBuilderInterface $formBuilder
    ) {
    }

    public static function create(ContainerInterface $container)
    {
        return new self(
            $container->get('class_resolver'),
            $container->get('form_builder')
        );
    }

    public function getForm(RouteMatchInterface $routeMatch)
    {
        $route = $routeMatch->getRouteObject();
        assert($route !== null);
        if ($route->hasOption('include file')) {
            $includePath = $route->getOption('include file');
            assert(is_string($includePath));
            if (file_exists($includePath)) {
                require_once $includePath;
            }
        }
        $form_object = $this->classResolver->getInstanceFromDefinition(
            DrupalGetForm::class
        );
        $form_object->setFormId($route->getDefault('_form_id'));
        $form_state = new ArrayAccessFormState();
        $arguments = (array) $route->getDefault('_custom_page_arguments');
        foreach ($arguments as &$argument) {
            if (is_string($argument) && $placeholder = preg_filter('/(^{)(.*)(}$)/', '$2', $argument)) {
                $argument = $routeMatch->getParameter($placeholder);
            }
        }
        $form_state->addBuildInfo('args', array_values($arguments));
        return $this->formBuilder->buildForm($form_object, $form_state);
    }
}
