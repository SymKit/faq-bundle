[![CI](https://github.com/symkit/faq-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/symkit/faq-bundle/actions)
[![Latest Version](https://img.shields.io/packagist/v/symkit/faq-bundle.svg)](https://packagist.org/packages/symkit/faq-bundle)
[![PHPStan Level 9](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg)](https://phpstan.org/)

# FAQ Bundle

A flexible and modular FAQ (Frequently Asked Questions) management bundle for Symfony applications. It provides a configurable administration interface (CRUD), public rendering with Stimulus accordion, and automatic position management for FAQ items.

## Requirements

- PHP 8.2+
- Symfony 7.0 or 8.0
- Doctrine ORM
- [symkit/form-bundle](https://packagist.org/packages/symkit/form-bundle)
- [symkit/crud-bundle](https://packagist.org/packages/symkit/crud-bundle)

## Installation

### 1. Require via Composer

```bash
composer require symkit/faq-bundle
```

### 2. Enable the bundle

Register the bundle in `config/bundles.php`:

```php
return [
    // ...
    Symkit\FaqBundle\SymkitFaqBundle::class => ['all' => true],
];
```

### 3. Update database schema

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Configuration

All features can be enabled or disabled via the bundle configuration. Create or edit `config/packages/symkit_faq.yaml`:

```yaml
symkit_faq:
    enabled: true

    admin:
        enabled: true
        route_prefix: /admin

    public:
        enabled: true
        route_prefix: /faq

    entity:
        # Override with your own classes if you extend the default entities
        faq_class: Symkit\FaqBundle\Entity\Faq
        faq_repository_class: Symkit\FaqBundle\Repository\FaqRepository
        faq_item_class: Symkit\FaqBundle\Entity\FaqItem
        faq_item_repository_class: Symkit\FaqBundle\Repository\FaqItemRepository

    doctrine:
        enabled: true

    twig:
        enabled: true

    asset_mapper:
        enabled: true
```

If you use custom entity or repository classes (e.g. extending the defaults), configure them here and ensure your Doctrine mapping covers the same tables or your overrides.

## Routes

Import the bundle routes in your application `config/routes.yaml`.

**Configurable prefixes** (recommended; uses `admin.route_prefix` and `public.route_prefix` from config):

```yaml
symkit_faq:
    resource: 'symkit_faq.route_loader::loadRoutes'
    type: service
```

**Fixed paths** (`/admin`, `/faq`):

```yaml
symkit_faq:
    resource: '@SymkitFaqBundle/config/routes.yaml'
```

This registers:

- **Admin**: `/admin/faqs` (list, create, edit, delete), `/admin/faqs/{faqId}/items/create`, `/admin/faq-items/{id}/edit`, `/admin/faq-items/{id}/delete`
- **Public**: `/faq/{code}` (show FAQ by code)

You can wrap the import with a custom `prefix` if you need to change the URL prefix.

## Translations

The bundle provides XLIFF translations in `SymkitFaqBundle` (and `validators` for constraint messages) in **English** and **French**. Translations are auto-discovered from the bundle `translations/` directory.

To override or add locales, place your files in your app’s `translations/` directory with the same domain names (`SymkitFaqBundle.*.xlf`, `validators.*.xlf`).

## Usage

### Displaying a FAQ in Twig

Render a FAQ block by code (e.g. from a controller that loaded the entity):

```twig
{{ include('@SymkitFaq/faq/components/_faq.html.twig', {
    faq: faq_entity
}) }}
```

### Admin interface

The admin CRUD is available at `/admin/faqs` (or your configured prefix). It uses [symkit/crud-bundle](https://packagist.org/packages/symkit/crud-bundle) for lists, forms, and actions.

### Customizing templates

Override bundle templates under your project:

```
templates/bundles/SymkitFaqBundle/faq/
```

Copy the relevant Twig files from `vendor/symkit/faq-bundle/templates/faq/` and edit them.

### Assets and Stimulus

The bundle registers its Stimulus controller via AssetMapper. Ensure your app uses AssetMapper and that the `faq` controller is loaded (e.g. in your `importmap.php` and Stimulus app registration). The accordion uses `data-controller="faq"` and `data-action="faq#toggle"`.

## Components

- **Entities**: `Faq` (group of questions), `FaqItem` (question/answer with position).
- **Stimulus**: `faq` controller for accordion toggle behaviour.

## Dependencies

- [doctrine/doctrine-bundle](https://packagist.org/packages/doctrine/doctrine-bundle) — ORM integration
- [doctrine/orm](https://packagist.org/packages/doctrine/orm) — Entity persistence
- [symfony/validator](https://packagist.org/packages/symfony/validator) — Validation
- [symkit/form-bundle](https://packagist.org/packages/symkit/form-bundle) — FormSectionType, SlugType, form theme
- [symkit/crud-bundle](https://packagist.org/packages/symkit/crud-bundle) — CRUD and list UI

## License

MIT.
