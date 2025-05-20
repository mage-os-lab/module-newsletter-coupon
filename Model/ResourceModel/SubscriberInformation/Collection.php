<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Model\ResourceModel\SubscriberInformation;

use MageOS\NewsletterCoupon\Model\ResourceModel\SubscriberInformation as SubscriberInformationResource;
use MageOS\NewsletterCoupon\Model\SubscriberInformation;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            SubscriberInformation::class,
            SubscriberInformationResource::class
        );
    }
}
