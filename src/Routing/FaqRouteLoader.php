<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads FAQ bundle routes with configurable admin and public path prefixes.
 *
 * In your app config/routes.yaml, use:
 *   symkit_faq:
 *     resource: 'symkit_faq.route_loader::loadRoutes'
 *     type: service
 */
final readonly class FaqRouteLoader
{
    public function __construct(
        private string $adminRoutePrefix,
        private string $publicRoutePrefix,
    ) {
    }

    public function loadRoutes(): RouteCollection
    {
        $routes = new RouteCollection();

        $adminPrefix = rtrim($this->adminRoutePrefix, '/');
        $publicPrefix = rtrim($this->publicRoutePrefix, '/');

        $routes->add('admin_faq_list', new Route($adminPrefix.'/faqs', [
            '_controller' => 'Symkit\FaqBundle\Controller\Admin\FaqController::list',
        ], [], [], '', [], ['GET']));
        $routes->add('admin_faq_create', new Route($adminPrefix.'/faqs/create', [
            '_controller' => 'Symkit\FaqBundle\Controller\Admin\FaqController::create',
        ], [], [], '', [], ['GET', 'POST']));
        $routes->add('admin_faq_edit', new Route($adminPrefix.'/faqs/{id}/edit', [
            '_controller' => 'Symkit\FaqBundle\Controller\Admin\FaqController::edit',
        ], ['id' => '\d+'], [], '', [], ['GET', 'POST']));
        $routes->add('admin_faq_delete', new Route($adminPrefix.'/faqs/{id}/delete', [
            '_controller' => 'Symkit\FaqBundle\Controller\Admin\FaqController::delete',
        ], ['id' => '\d+'], [], '', [], ['POST']));
        $routes->add('admin_faq_item_create', new Route($adminPrefix.'/faqs/{faqId}/items/create', [
            '_controller' => 'Symkit\FaqBundle\Controller\Admin\FaqItemController::create',
        ], ['faqId' => '\d+'], [], '', [], ['GET', 'POST']));
        $routes->add('admin_faq_item_edit', new Route($adminPrefix.'/faq-items/{id}/edit', [
            '_controller' => 'Symkit\FaqBundle\Controller\Admin\FaqItemController::edit',
        ], ['id' => '\d+'], [], '', [], ['GET', 'POST']));
        $routes->add('admin_faq_item_delete', new Route($adminPrefix.'/faq-items/{id}/delete', [
            '_controller' => 'Symkit\FaqBundle\Controller\Admin\FaqItemController::delete',
        ], ['id' => '\d+'], [], '', [], ['POST']));

        $routes->add('symkit_faq_show', new Route($publicPrefix.'/{code}', [
            '_controller' => 'Symkit\FaqBundle\Controller\FaqController::show',
        ], [], [], '', [], ['GET']));

        return $routes;
    }
}
