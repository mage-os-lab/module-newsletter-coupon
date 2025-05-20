<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\SalesRule\Model\Coupon;

interface SubscriberInformationInterface extends ExtensibleDataInterface
{
    const TABLE_IDENTIFIER = "newsletter_subscriber_coupon";
    const ID = 'entity_id';
    const SUBSCRIBER_ID = 'subscriber_id';
    const SUBSCRIBER_EMAIL = 'subscriber_email';
    const COUPON_ID = 'coupon_id';
    const CREATED_AT = 'created_at';
    const IS_ENABLED = 'is_enabled';

    /**
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * @return string|null
     */
    public function getSubscriberId(): ?string;

    /**
     * @param $subscriberId
     * @return SubscriberInformationInterface
     */
    public function setSubscriberId($subscriberId): SubscriberInformationInterface;

    /**
     * @return string|null
     */
    public function getSubscriberEmail(): ?string;

    /**
     * @param string $email
     * @return SubscriberInformationInterface
     */
    public function setSubscriberEmail(string $email): SubscriberInformationInterface;

    /**
     * @return Subscriber|null
     */
    public function getSubscriber(): ?Subscriber;

    /**
     * @return boolean|null
     */
    public function getIsEnabled(): ?bool;

    /**
     * @param bool $isSubscriptionActive
     * @return SubscriberInformationInterface
     */
    public function setIsEnabled(bool $isSubscriptionActive): SubscriberInformationInterface;

    /**
     * @return string|null
     */
    public function getCouponId(): ?string;

    /**
     * @param $couponId
     * @return SubscriberInformationInterface
     */
    public function setCouponId($couponId): SubscriberInformationInterface;

    /**
     * @return Coupon|null
     */
    public function getCoupon(): ?Coupon;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

}
