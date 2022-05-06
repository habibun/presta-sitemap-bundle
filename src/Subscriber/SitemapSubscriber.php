<?php

namespace App\Subscriber;

use App\Repository\ProductRepository;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, ProductRepository $productRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerProductsUrls($event->getUrlContainer());
    }

    public function registerProductsUrls(UrlContainerInterface $urls): void
    {
        $products = $this->productRepository->findAll();

        foreach ($products as $post) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'app_product_show',
                        ['id' => $post->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'product'
            );
        }
    }
}
