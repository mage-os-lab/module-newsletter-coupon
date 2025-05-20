<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SubscriberInformation extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('newsletter_subscriber_coupon', 'entity_id');
    }
}
