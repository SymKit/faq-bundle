<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Integration;

use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symkit\FaqBundle\SymkitFaqBundle;

final class BundleBootTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        restore_exception_handler();
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * @param array<string, mixed> $options
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(SymkitFaqBundle::class);
        $kernel->addTestConfig(static function ($container): void {
            $container->loadFromExtension('framework', [
                'secret' => 'test',
                'test' => true,
                'form' => ['enabled' => true],
                'csrf_protection' => false,
                'asset_mapper' => ['enabled' => false],
            ]);
            $container->loadFromExtension('symkit_faq', [
                'enabled' => true,
                'doctrine' => ['enabled' => false],
                'admin' => ['enabled' => false],
                'public' => ['enabled' => false],
            ]);
        });
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testBundleBootsWithoutError(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        self::assertTrue($container->hasParameter('symkit_faq.entity.faq_class'));
        self::assertSame('Symkit\FaqBundle\Entity\Faq', $container->getParameter('symkit_faq.entity.faq_class'));
    }
}
