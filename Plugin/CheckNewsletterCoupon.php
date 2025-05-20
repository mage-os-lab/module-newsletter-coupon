<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Plugin;

use Magento\Checkout\Controller\Cart\CouponPost;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Model\CouponFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use MageOS\NewsletterCoupon\Api\SubscriberInformationRepositoryInterface;
use MageOS\NewsletterCoupon\Helper\ConfigurationHelper;

class CheckNewsletterCoupon
{
    const DEFAULT_DELAY_EXPRESSION = '+30days';

    /**
     * CheckNewsletterCoupon constructor.
     * @param CouponFactory $couponFactory
     * @param MessageManager $messageManager
     * @param Escaper $escaper
     * @param SubscriberInformationRepositoryInterface $subscriberInformationRepository
     * @param ConfigurationHelper $configurationHelper
     */
    public function __construct(
        protected CouponFactory $couponFactory,
        protected MessageManager $messageManager,
        protected Escaper $escaper,
        protected SubscriberInformationRepositoryInterface $subscriberInformationRepository,
        protected ConfigurationHelper $configurationHelper
    )
    {
    }

    /**
     * @param CouponPost $subject
     * @param callable $proceed
     * @return array|void
     * @throws NoSuchEntityException|LocalizedException
     */
    public function aroundExecute(CouponPost $subject, callable $proceed)
    {
        if ($this->configurationHelper->isCouponGenerationAvailable()) {

            $couponCode = $subject->getRequest()->getParam('remove') == 1
                ? ''
                : trim($subject->getRequest()->getParam('coupon_code'));

            $coupon = $this->couponFactory->create();
            $coupon->load($couponCode, 'code');

            if ($coupon !== null && $coupon->getCouponId()) {

                $subscriberInformation = $this->subscriberInformationRepository->getByCouponId($coupon->getCouponId());

                if ($subscriberInformation->getId()) {

                    if (!$subscriberInformation->getIsEnabled()) {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon "%1" can\'t be utilized: subscription is not valid or coupon was already used.',
                                $this->escaper->escapeHtml($couponCode)
                            )
                        );
                        $subject->getRequest()->setParam('coupon_code', '');
                    }

                    $createdAt = $subscriberInformation->getCreatedAt();
                    $delayExpression = $this->configurationHelper->getDelayExpression();
                    if ($delayExpression === null) {
                        $delayExpression = self::DEFAULT_DELAY_EXPRESSION;
                    }
                    $expirationDate = strtotime($createdAt . $delayExpression);
                    $today = strtotime(date("Y-m-d"));
                    if ($expirationDate < $today) {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon "%1" is expired.',
                                $this->escaper->escapeHtml($couponCode)
                            )
                        );
                        $subject->getRequest()->setParam('coupon_code', '');
                    }
                }
            }
        }

        return $proceed();
    }
}
