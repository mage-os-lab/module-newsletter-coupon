<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageOS\NewsletterCoupon\Model\SubscriberInformation;

interface SubscriberInformationRepositoryInterface
{
    /**
     * @param SubscriberInformation $subscriberInformation
     * @return SubscriberInformation
     */
    public function save(SubscriberInformation $subscriberInformation): SubscriberInformation;

    /**
     * @param int $subscriberInformationId
     * @return SubscriberInformation
     * @throws NoSuchEntityException
     */
    public function getById(int $subscriberInformationId): SubscriberInformation;

    /**
     * @param $subscriberId
     * @return SubscriberInformation|null
     * @throws NoSuchEntityException
     */
    public function getBySubscriberId($subscriberId): ?SubscriberInformation;

    /**
     * @param $couponId
     * @return SubscriberInformation|null
     * @throws NoSuchEntityException
     */
    public function getByCouponId($couponId): ?SubscriberInformation;

    /**
     * @param SubscriberInformation $subscriberInformation
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(SubscriberInformation $subscriberInformation): bool;

    /**
     * @param int $subscriberInformationId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $subscriberInformationId): bool;
}
