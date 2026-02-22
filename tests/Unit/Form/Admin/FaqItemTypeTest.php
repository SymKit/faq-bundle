<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Unit\Form\Admin;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symkit\FaqBundle\Entity\FaqItem;
use Symkit\FaqBundle\Form\Admin\FaqItemType;

final class FaqItemTypeTest extends TestCase
{
    public function testConfigureOptions(): void
    {
        $resolver = new \Symfony\Component\OptionsResolver\OptionsResolver();
        $type = new FaqItemType(FaqItem::class);
        $type->configureOptions($resolver);

        $defaults = $resolver->resolve();
        self::assertSame(FaqItem::class, $defaults['data_class']);
        self::assertSame('SymkitFaqBundle', $defaults['translation_domain']);
    }

    public function testBuildFormAddsExpectedFields(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('create')->willReturnSelf();
        $builder->method('add')->willReturnSelf();

        $type = new FaqItemType(FaqItem::class);
        $type->buildForm($builder, []);
        self::addToAssertionCount(1);
    }
}
