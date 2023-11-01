<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Entity;

use Drupal\Core\Entity\HtmlEntityFormController as CoreHtmlEntityFormController;
use Drupal\Core\Routing\RouteMatchInterface;
use Retrofit\Drupal\Form\ArrayAccessFormState;
use Symfony\Component\HttpFoundation\Request;

class HtmlEntityFormController extends CoreHtmlEntityFormController
{
  /**
   * @return mixed[]
   */
    public function getContentResult(Request $request, RouteMatchInterface $route_match): array
    {
        $form_arg = $this->getFormArgument($route_match);
        $form_object = $this->getFormObject($route_match, $form_arg);
        $form_state = new ArrayAccessFormState();
        $request->attributes->set('form', []);
        $request->attributes->set('form_state', $form_state);
        $args = $this->argumentResolver->getArguments($request, [$form_object, 'buildForm']);
        $request->attributes->remove('form');
        $request->attributes->remove('form_state');
        unset($args[0], $args[1]);
        $form_state->addBuildInfo('args', array_values($args));
        return $this->formBuilder->buildForm($form_object, $form_state);
    }
}
