<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Plugin;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\Data\Rule;
use MageOS\NewsletterCoupon\Api\SubscriberInformationRepositoryInterface;
use MageOS\NewsletterCoupon\Model\SubscriberInformation;
use MageOS\NewsletterCoupon\Api\Data\SubscriberInformationInterfaceFactory;
use Magento\Newsletter\Model\Subscriber;
use MageOS\NewsletterCoupon\Helper\ConfigurationHelper;
use Magento\SalesRule\Model\RuleRepository;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Model\Rule as SalesRule;

class SaveSubscriberData
{
    /**
     * @var SubscriberInformationRepositoryInterface
     */
    protected $subscriberInformationRepository;

    /**
     * @var SubscriberInformationInterfaceFactory
     */
    protected $subscriberInformationFactory;

    /**
     * @var ConfigurationHelper
     */
    protected $configurationHelper;

    /**
     * @var RuleRepository
     */
    protected $salesRuleRepository;

    /**
     * @var SalesRule
     */
    protected $salesRule;

    /**
     * @var CouponRepositoryInterface
     */
    protected $couponRepository;

    /**
     * SaveSubscriberData constructor.
     * @param SubscriberInformationRepositoryInterface $subscriberInformationRepository
     * @param SubscriberInformationInterfaceFactory $subscriberInformationFactory
     * @param ConfigurationHelper $configurationHelper
     * @param RuleRepository $salesRuleRepository
     * @param SalesRule $salesRule
     * @param CouponRepositoryInterface $couponRepository
     */
    public function __construct(
        SubscriberInformationRepositoryInterface $subscriberInformationRepository,
        SubscriberInformationInterfaceFactory $subscriberInformationFactory,
        ConfigurationHelper $configurationHelper,
        RuleRepository $salesRuleRepository,
        SalesRule $salesRule,
        CouponRepositoryInterface $couponRepository
    )
    {
        $this->subscriberInformationRepository = $subscriberInformationRepository;
        $this->subscriberInformationFactory = $subscriberInformationFactory;
        $this->configurationHelper = $configurationHelper;
        $this->salesRuleRepository = $salesRuleRepository;
        $this->salesRule = $salesRule;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @param Subscriber $subject
     * @return array
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeAfterSave(Subscriber $subject): array
    {
        if ($subject->getStatus() === Subscriber::STATUS_SUBSCRIBED) {
            $subscriberInformation = $this->subscriberInformationRepository->getBySubscriberId($subject->getSubscriberId());
            if ($subscriberInformation !== null && $subscriberInformation->getId()) {
                $subscriberInformation->setIsEnabled(true);
                $this->subscriberInformationRepository->save($subscriberInformation);
            } else {
                /**
                 * @var SubscriberInformation $subscriberInformation
                 */
                $subscriberInformation = $this->subscriberInformationFactory->create();
                $subscriberInformation->setSubscriberId($subject->getSubscriberId());
                $subscriberInformation->setSubscriberEmail($subject->getSubscriberEmail());
                $subscriberInformation->setIsEnabled(true);
                if ($this->configurationHelper->isCouponGenerationEnabled()) {
                    /**
                     * @var Coupon $salesRuleCoupon
                     */
                    $salesRuleCoupon = $this->generateCoupon();
                    if ($salesRuleCoupon !== false && $salesRuleCoupon->getCouponId()) {
                        $subscriberInformation->setCouponId($salesRuleCoupon->getCouponId());
                    } else {
                        throw new LocalizedException(__('Can\'t acquire coupon, salesrule coupon type must be "Auto".'));
                    }
                }
                $this->subscriberInformationRepository->save($subscriberInformation);
            }
        }

        if ($subject->getStatus() === Subscriber::STATUS_UNSUBSCRIBED || $subject->getStatus() === Subscriber::STATUS_NOT_ACTIVE) {
            $subscriberInformation = $this->subscriberInformationRepository->getBySubscriberId($subject->getSubscriberId());
            if ($subscriberInformation->getId() !== null) {
                $subscriberInformation->setIsEnabled(false);
                $this->subscriberInformationRepository->save($subscriberInformation);
            }
        }
        return [];
    }

    /**
     * @return bool|Coupon
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCoupon(): bool|Coupon
    {
        $coupon = false;
        /**
         * @var Rule $usedSalesRule
         */
        $usedSalesRule = $this->salesRuleRepository->getById(intval($this->configurationHelper->getUsedSalesRuleId()));
        $couponType = $usedSalesRule->getCouponType();
        $couponUseAutoGeneration = $usedSalesRule->getUseAutoGeneration();

        if ($couponType === Rule::COUPON_TYPE_SPECIFIC_COUPON && $couponUseAutoGeneration == 1) {
            $this->salesRule->setRuleId($usedSalesRule->getRuleId());
            /**
             * @var Coupon $coupon
             */
            $coupon = $this->salesRule->acquireCoupon();
        }
        return $coupon;
    }
}
