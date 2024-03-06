<?php

namespace App\Filter;

use App\DTO\PriceEnquiryInterface;
use App\Entity\Promotion;
use App\Filter\Modifier\Factory\PriceModifierFactoryInterface;

readonly class LowestPriceFilter implements PriceFilterInterface
{

    public function __construct(
        private PriceModifierFactoryInterface $priceModifierFactory
    )
    {
    }

    public function apply(PriceEnquiryInterface $enquiry, Promotion ...$promotions): PriceEnquiryInterface
    {
        $price = $enquiry->getProduct()->getPrice();
        $enquiry->setPrice($price);
        $quantity = $enquiry->getQuantity();
        $lowestPrice = $quantity * $price;
        // loop over the promotions
        foreach ($promotions as $promotion):
            // run the promotions modification logic against the enquiry
            // 1. check does the promotion apply e.g. is it in date range / is the voucher code valid?
            // 2. apply the price modification to obtain a $modifiedPrice (how?)
            $priceModifier = $this->priceModifierFactory->create($promotion->getType());
            $modifiedPrice = $priceModifier->modify($price, $quantity, $promotion, $enquiry);
            // 3. check if $modifiedPrice < $lowestPrice
            if($modifiedPrice < $lowestPrice) {
                // 1. Save to Enquiry properties
                $enquiry->setDiscountedPrice($modifiedPrice);
                $enquiry->setPromotionId($promotion->getId());
                $enquiry->setPromotionName($promotion->getName());

                // 2. update $lowestPrice
                $lowestPrice = $modifiedPrice;
            }
        endforeach;
        return $enquiry;
    }
}