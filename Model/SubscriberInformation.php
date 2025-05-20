<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Newsletter\Model\Subscriber;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Model\Coupon;
use MageOS\NewsletterCoupon\Api\Data\SubscriberInformationInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\SalesRule\Model\CouponRepository as SalesRuleCouponRepository;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use MageOS\NewsletterCoupon\Model\ResourceModel\SubscriberInformation as SubscriberInformationResource;
use MageOS\NewsletterCoupon\Model\ResourceModel\SubscriberInformation\Collection as SubscriberInformationCollection;

class SubscriberInformation extends AbstractModel implements SubscriberInformationInterface
{

    const ENTITY = 'newsletter_subscriber_coupon';

    /**
     * @var array
     */
    protected $interfaceAttributes = [
        SubscriberInformationInterface::ID,
        SubscriberInformationInterface::SUBSCRIBER_ID,
        SubscriberInformationInterface::SUBSCRIBER_EMAIL,
        SubscriberInformationInterface::IS_ENABLED,
        SubscriberInformationInterface::COUPON_ID
    ];

    /**
     * @param SalesRuleCouponRepository $couponRepository
     * @param SubscriberFactory $subscriberFactory
     * @param Context $context
     * @param Registry $registry
     * @param SubscriberInformationResource|null $resource
     * @param SubscriberInformationCollection|null $resourceCollection
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        protected SalesRuleCouponRepository $couponRepository,
        protected SubscriberFactory $subscriberFactory,
        Context $context,
        Registry $registry,
        SubscriberInformationResource $resource = null,
        SubscriberInformationCollection $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    protected function _construct()
    {
        $this->_init(SubscriberInformationResource::class);
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getData(self::ID);
    }

    /**
     * @return string|null
     */
    public function getSubscriberId(): ?string
    {
        return $this->getData(self::SUBSCRIBER_ID);
    }

    /**
     * @param string $subscriberId
     * @return SubscriberInformationInterface|SubscriberInformation
     */
    public function setSubscriberId($subscriberId): SubscriberInformationInterface|SubscriberInformation
    {
        return $this->setData(self::SUBSCRIBER_ID, $subscriberId);
    }

    /**
     * @return string|null
     */
    public function getSubscriberEmail(): ?string
    {
        return $this->getData(self::SUBSCRIBER_EMAIL);
    }

    /**
     * @param string $email
     * @return SubscriberInformationInterface|SubscriberInformation
     */
    public function setSubscriberEmail($email): SubscriberInformationInterface|SubscriberInformation
    {
        return $this->setData(self::SUBSCRIBER_EMAIL, $email);
    }

    /**
     * @return Subscriber|null
     * @throws NoSuchEntityException
     */
    public function getSubscriber(): ?Subscriber
    {
        $subscriberEmail = $this->getSubscriberEmail();
        if ($subscriberEmail !== null) {
            $subscriber = $this->subscriberFactory->create()->loadByEmail($subscriberEmail);
        } else {
            throw new NoSuchEntityException();
        }
        return $subscriber;
    }

    /**
     * @return bool|null
     */
    public function getIsEnabled(): ?bool
    {
        return (bool)$this->getData(self::IS_ENABLED);
    }

    /**
     * @param bool $isSubscriptionActive
     * @return SubscriberInformationInterface|SubscriberInformation
     */
    public function setIsEnabled($isSubscriptionActive): SubscriberInformationInterface|SubscriberInformation
    {
        return $this->setData(self::IS_ENABLED, $isSubscriptionActive);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @return string|null
     */
    public function getCouponId(): ?string
    {
        return $this->getData(self::COUPON_ID);
    }

    /**
     * @param int $couponId
     * @return SubscriberInformationInterface|SubscriberInformation
     */
    public function setCouponId($couponId): SubscriberInformationInterface|SubscriberInformation
    {
        return $this->setData(self::COUPON_ID, $couponId);
    }

    /**
     * @return Coupon|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCoupon(): ?Coupon
    {
        $couponId = $this->getCouponId();
        if ($couponId !== null) {
            $coupon = $this->couponRepository->getById($couponId);
        } else {
            throw new NoSuchEntityException();
        }
        return $coupon;
    }
}
