<?php

namespace App\Controller;

use App\Cache\PromotionCache;
use App\DTO\LowestPriceEnquiry;
use App\Filter\PromotionsFilterInterface;
use App\Repository\ProductRepository;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Vinícius Valle Beraldo <vvberaldo@proton.me>
 */
class ProductsController extends AbstractController
{

    public function __construct(
        private readonly ProductRepository      $repository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }


    #[Route('/products/{id}/lowest-price', name: 'lowest-price', methods: 'POST')]
    public function lowestPrice(
        Request                   $request,
        int                       $id,
        DTOSerializer             $serializer,
        PromotionsFilterInterface $promotionsFilter,
        PromotionCache            $promotionCache
    ): Response
    {

        // 1. Deserialize json data into a EnquiryDTO
        /**
         * @var LowestPriceEnquiry $lowestPriceEnquiry
         */
        $lowestPriceEnquiry = $serializer->deserialize(
            $request->getContent(), LowestPriceEnquiry::class, 'json'
        );

        $product = $this->repository->findOrFail($id);

        $lowestPriceEnquiry->setProduct($product);

        $promotions = $promotionCache->findValidForProduct($product, $lowestPriceEnquiry->getRequestDate());

        // 2. Pass the Enquery into a promotion filter
        $modifiedEnquiry = $promotionsFilter->apply($lowestPriceEnquiry, ...$promotions);

        // 3. Return the modified Enquiry
        $responseContent = $serializer->serialize(data: $modifiedEnquiry, format: 'json');
        return new JsonResponse(data: $responseContent, status: Response::HTTP_OK, json: true);
    }
}

//docker-compose up -d
//docker-compose down
// symfony console cache:pool:delete cache.app <nome>