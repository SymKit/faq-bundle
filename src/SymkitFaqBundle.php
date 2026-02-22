<?php

declare(strict_types=1);

namespace Symkit\FaqBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symkit\FaqBundle\Contract\FaqByCodeResolverInterface;
use Symkit\FaqBundle\Contract\FaqByIdResolverInterface;
use Symkit\FaqBundle\Entity\Faq;
use Symkit\FaqBundle\Entity\FaqItem;
use Symkit\FaqBundle\Repository\FaqItemRepository;
use Symkit\FaqBundle\Repository\FaqRepository;

class SymkitFaqBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('route_prefix')->defaultValue('/admin')->end()
                    ->end()
                ->end()
                ->arrayNode('public')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('route_prefix')->defaultValue('/faq')->end()
                    ->end()
                ->end()
                ->arrayNode('entity')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('faq_class')->defaultValue(Faq::class)->end()
                        ->scalarNode('faq_repository_class')->defaultValue(FaqRepository::class)->end()
                        ->scalarNode('faq_item_class')->defaultValue(FaqItem::class)->end()
                        ->scalarNode('faq_item_repository_class')->defaultValue(FaqItemRepository::class)->end()
                    ->end()
                ->end()
                ->arrayNode('doctrine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('asset_mapper')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array{
     *     enabled: bool,
     *     admin: array{enabled: bool, route_prefix: string},
     *     public: array{enabled: bool, route_prefix: string},
     *     entity: array{
     *         faq_class: string,
     *         faq_repository_class: string,
     *         faq_item_class: string,
     *         faq_item_repository_class: string
     *     },
     *     doctrine: array{enabled: bool},
     *     twig: array{enabled: bool},
     *     asset_mapper: array{enabled: bool}
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$config['enabled']) {
            return;
        }

        $params = $container->parameters();
        $params->set('symkit_faq.entity.faq_class', $config['entity']['faq_class']);
        $params->set('symkit_faq.entity.faq_repository_class', $config['entity']['faq_repository_class']);
        $params->set('symkit_faq.entity.faq_item_class', $config['entity']['faq_item_class']);
        $params->set('symkit_faq.entity.faq_item_repository_class', $config['entity']['faq_item_repository_class']);
        $params->set('symkit_faq.admin.route_prefix', $config['admin']['route_prefix']);
        $params->set('symkit_faq.public.route_prefix', $config['public']['route_prefix']);

        $services = $container->services()->defaults()->autowire()->autoconfigure();

        if ($config['doctrine']['enabled']) {
            $services->set($config['entity']['faq_repository_class'])
                ->arg('$entityClass', '%symkit_faq.entity.faq_class%')
                ->tag('doctrine.repository_service');
            $services->set($config['entity']['faq_item_repository_class'])
                ->arg('$entityClass', '%symkit_faq.entity.faq_item_class%')
                ->tag('doctrine.repository_service');
            $services->alias(FaqByCodeResolverInterface::class, $config['entity']['faq_repository_class']);
            $services->alias(FaqByIdResolverInterface::class, $config['entity']['faq_repository_class']);
        }

        if ($config['admin']['enabled']) {
            $services->set(Controller\Admin\FaqController::class)
                ->arg('$faqClass', '%symkit_faq.entity.faq_class%')
                ->arg('$faqFormClass', Form\Admin\FaqType::class)
                ->tag('controller.service_arguments');
            $services->set(Controller\Admin\FaqItemController::class)
                ->arg('$faqItemClass', '%symkit_faq.entity.faq_item_class%')
                ->arg('$faqItemFormClass', Form\Admin\FaqItemType::class)
                ->tag('controller.service_arguments');
            $services->set(Form\Admin\FaqType::class)
                ->arg('$dataClass', '%symkit_faq.entity.faq_class%');
            $services->set(Form\Admin\FaqItemType::class)
                ->arg('$dataClass', '%symkit_faq.entity.faq_item_class%');
        }

        if ($config['public']['enabled']) {
            $services->set(Controller\FaqController::class)->tag('controller.service_arguments');
        }

        $services->set('symkit_faq.route_loader', Routing\FaqRouteLoader::class)
            ->arg('$adminRoutePrefix', '%symkit_faq.admin.route_prefix%')
            ->arg('$publicRoutePrefix', '%symkit_faq.public.route_prefix%');

        if ($config['doctrine']['enabled']) {
            $services->set(Manager\FaqItemPositioner::class)
                ->arg('$faqItemClass', '%symkit_faq.entity.faq_item_class%');
            $services->set(EventListener\FaqItemPositionListener::class)
                ->arg('$faqItemClass', '%symkit_faq.entity.faq_item_class%')
                ->tag('kernel.event_listener', ['event' => 'crud.post_persist', 'method' => 'onCrudEvent'])
                ->tag('kernel.event_listener', ['event' => 'crud.post_update', 'method' => 'onCrudEvent']);
        }
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $configs = $builder->getExtensionConfig('symkit_faq');
        /** @var array{enabled?: bool, twig?: array{enabled?: bool}, asset_mapper?: array{enabled?: bool}} $merged */
        $merged = array_merge(...array_map(
            static fn (array $c): array => $c,
            array_values($configs),
        ));
        $enabled = $merged['enabled'] ?? true;
        $twigEnabled = ($merged['twig']['enabled'] ?? true) && $enabled;
        $assetMapperEnabled = ($merged['asset_mapper']['enabled'] ?? true) && $enabled;

        if ($twigEnabled) {
            $container->extension('twig', [
                'paths' => [
                    $this->getPath().'/templates' => 'SymkitFaq',
                ],
            ], true);
        }

        if ($assetMapperEnabled) {
            $container->extension('framework', [
                'asset_mapper' => [
                    'paths' => [
                        $this->getPath().'/assets/controllers' => 'faq',
                    ],
                ],
            ], true);
        }
    }
}
