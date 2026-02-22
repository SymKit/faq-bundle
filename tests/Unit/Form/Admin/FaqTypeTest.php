<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Unit\Form\Admin;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symkit\FaqBundle\Entity\Faq;
use Symkit\FaqBundle\Form\Admin\FaqType;
use Symkit\FormBundle\Form\Type\FormSectionType;
use Symkit\FormBundle\Form\Type\SlugType;

final class FaqTypeTest extends TestCase
{
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $type = new FaqType(Faq::class);
        $type->configureOptions($resolver);

        $defaults = $resolver->resolve();
        self::assertSame(Faq::class, $defaults['data_class']);
        self::assertSame('SymkitFaqBundle', $defaults['translation_domain']);
    }

    public function testBuildFormAddsExpectedFields(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('create')->willReturnSelf();
        $builder->method('add')->willReturnSelf();

        $type = new FaqType(Faq::class);
        $type->buildForm($builder, []);
        self::addToAssertionCount(1);
    }

    public function testBuildFormSectionHasInheritDataLabelAndIcon(): void
    {
        $calls = [];
        $sectionBuilder = $this->createMock(FormBuilderInterface::class);
        $sectionBuilder->method('add')->willReturnCallback(function (string $name, string $type, array $options = []) use ($sectionBuilder, &$calls): FormBuilderInterface {
            $calls['add'][] = ['name' => $name, 'type' => $type, 'options' => $options];

            return $sectionBuilder;
        });

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('create')->willReturnCallback(function (string $name, string $type, array $options = []) use ($sectionBuilder, &$calls): FormBuilderInterface {
            $calls['create'] = ['name' => $name, 'type' => $type, 'options' => $options];

            return $sectionBuilder;
        });
        $builder->method('add')->willReturnSelf();

        $type = new FaqType(Faq::class);
        $type->buildForm($builder, []);

        self::assertArrayHasKey('create', $calls);
        self::assertSame('general', $calls['create']['name']);
        self::assertSame(FormSectionType::class, $calls['create']['type']);
        self::assertTrue($calls['create']['options']['inherit_data'] ?? false);
        self::assertSame('form.faq.section.general', $calls['create']['options']['label'] ?? '');
        self::assertSame('heroicons:question-mark-circle-20-solid', $calls['create']['options']['section_icon'] ?? '');
        self::assertSame('form.faq.section.description', $calls['create']['options']['section_description'] ?? '');

        self::assertArrayHasKey('add', $calls);
        self::assertCount(2, $calls['add']);

        self::assertSame('title', $calls['add'][0]['name']);
        self::assertSame(TextType::class, $calls['add'][0]['type']);
        self::assertSame('form.faq.title', $calls['add'][0]['options']['label'] ?? '');
        self::assertSame(['placeholder' => 'form.faq.title_placeholder'], $calls['add'][0]['options']['attr'] ?? []);

        self::assertSame('code', $calls['add'][1]['name']);
        self::assertSame(SlugType::class, $calls['add'][1]['type']);
        self::assertSame('form.faq.code', $calls['add'][1]['options']['label'] ?? '');
        self::assertFalse($calls['add'][1]['options']['required'] ?? true);
        self::assertSame('title', $calls['add'][1]['options']['target'] ?? '');
        self::assertTrue($calls['add'][1]['options']['unique'] ?? false);
        self::assertSame(Faq::class, $calls['add'][1]['options']['entity_class'] ?? null);
        self::assertSame('code', $calls['add'][1]['options']['slug_field'] ?? '');
        self::assertSame('form.faq.code_help', $calls['add'][1]['options']['help'] ?? '');
    }
}
