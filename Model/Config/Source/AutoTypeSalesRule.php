<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Model\Config\Source;

use Magento\SalesRule\Model\Data\Rule;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection;
use \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;

class AutoTypeSalesRule implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * AutoTypeSalesRules constructor.
     * @param RuleCollectionFactory $ruleCollectionFactory
     */
    public function __construct(
        protected RuleCollectionFactory $ruleCollectionFactory
    )
    {
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        /**
         * @var Collection $ruleCollection
         */
        $ruleCollection = $this->ruleCollectionFactory->create();
        $ruleCollection->addFieldToFilter(
            'coupon_type',
            \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC
        )->addFieldToFilter('use_auto_generation', 1);
        $options = [];

        /**
         * @var Rule $rule
         */
        foreach ($ruleCollection->getItems() as $rule) {
            $options[] = array(
                'value' => $rule->getRuleId(),
                'label' => $rule->getName()
            );
        }
        return $options;
    }
}
