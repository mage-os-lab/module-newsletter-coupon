<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use MageOS\NewsletterCoupon\Api\Data\SubscriberInformationInterfaceFactory;
use MageOS\NewsletterCoupon\Model\ResourceModel\SubscriberInformation\CollectionFactory as SubscriberInformationCollectionFactory;
use MageOS\NewsletterCoupon\Model\ResourceModel\SubscriberInformation as SubscriberInformationResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageOS\NewsletterCoupon\Api\SubscriberInformationRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class SubscriberInformationRepository implements SubscriberInformationRepositoryInterface
{

    /**
     * SubscriberInformationRepository constructor.
     * @param SubscriberInformationInterfaceFactory $subscriberInformationFactory
     * @param SubscriberInformationCollectionFactory $subscriberInformationCollectionFactory
     * @param SubscriberInformationResource $subscriberInformationResource
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $marketingEmailIntegrations
     */
    public function __construct(
        protected SubscriberInformationInterfaceFactory $subscriberInformationFactory,
        protected SubscriberInformationCollectionFactory $subscriberInformationCollectionFactory,
        protected SubscriberInformationResource $subscriberInformationResource,
        protected CustomerRepositoryInterface $customerRepository,
        protected array $marketingEmailIntegrations = []
    )
    {
    }

    /**
     * @param int $subscriberInformationId
     * @return SubscriberInformation
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getById(int $subscriberInformationId): SubscriberInformation
    {
        /**
         * @var SubscriberInformation $subscriberInformation
         */
        $subscriberInformation = $this->subscriberInformationFactory->create();
        $subscriberInformation->load($subscriberInformationId);
        if (!$subscriberInformation->getId()) {
            throw new NoSuchEntityException(
                __("The subscriber information that was requested doesn't exist. Verify the id and try again.")
            );
        }
        return $subscriberInformation;
    }

    /**
     * @param $subscriberId
     * @return SubscriberInformation|null
     */
    public function getBySubscriberId($subscriberId): ?SubscriberInformation
    {
        $subscriberInformationCollection = $this->subscriberInformationCollectionFactory->create();
        return $subscriberInformationCollection
            ->addFieldToFilter('subscriber_id', $subscriberId)
            ->getFirstItem();
    }

    /**
     * @param $subscriberEmail
     * @return SubscriberInformation|null
     */
    public function getBySubscriberEmail($subscriberEmail): ?SubscriberInformation
    {
        $subscriberInformationCollection = $this->subscriberInformationCollectionFactory->create();
        return $subscriberInformationCollection
            ->addFieldToFilter('subscriber_email', $subscriberEmail)
            ->getFirstItem();
    }


    /**
     * @param $couponId
     * @return SubscriberInformation|null
     */
    public function getByCouponId($couponId): ?SubscriberInformation
    {
        $subscriberInformationCollection = $this->subscriberInformationCollectionFactory->create();
        return $subscriberInformationCollection
            ->addFieldToFilter('coupon_id', $couponId)
            ->getFirstItem();
    }

    /**
     * @param SubscriberInformation $subscriberInformation
     * @return SubscriberInformation
     * @throws CouldNotSaveException
     */
    public function save(SubscriberInformation $subscriberInformation): SubscriberInformation
    {
        try {
            $this->subscriberInformationResource->save($subscriberInformation);
            foreach ($this->marketingEmailIntegrations as $integration) {
                if ($integration instanceof AbstractNewsletterCouponIntegration) {
                    if ($subscriberInformation->getIsEnabled()) {
                        try {
                            $customer = $this->customerRepository->get($subscriberInformation->getSubscriberEmail());
                        } catch (\Exception $e) {
                            $customer = null;
                        }
                        $integration->updateSubscriber($subscriberInformation, $customer);
                    } else {
                        $integration->deleteSubscriber($subscriberInformation);
                    }
                }
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        }
        return $subscriberInformation;
    }

    /**
     * @param SubscriberInformation $subscriberInformation
     * @return bool
     * @throws StateException
     */
    public function delete(SubscriberInformation $subscriberInformation): bool
    {
        try {
            $this->subscriberInformationResource->delete($subscriberInformation);
            foreach ($this->marketingEmailIntegrations as $integration) {
                if ($integration instanceof AbstractNewsletterCouponIntegration) {
                    $integration->deleteSubscriber($subscriberInformation);
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new StateException(
                __('The "%1" subscriber information couldn\'t be removed.', $subscriberInformation->getId()),
                $e
            );
        }
    }

    /**
     * @param int $subscriberInformationId
     * @return bool
     * @throws NoSuchEntityException
     * @throws StateException|LocalizedException
     */
    public function deleteById(int $subscriberInformationId): bool
    {
        $subscriberInformation = $this->getById($subscriberInformationId);
        return $this->delete($subscriberInformation);
    }
}
