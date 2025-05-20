<?php
namespace MageOS\NewsletterCoupon\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use MageOS\NewsletterCoupon\Api\Data\SubscriberInformationInterface;

class AbstractNewsletterCouponIntegration
{
    /**
     * @param string $email
     * @return bool
     */
    public function subscriberExists(string $email) {
        return false;
    }

    /**
     * @param SubscriberInformationInterface $subscriptionInformation
     * @param CustomerInterface $customer
     */
    protected function updateSubscriberInformations(SubscriberInformationInterface $subscriptionInformation, CustomerInterface $customer) {}

    /**
     * @param SubscriberInformationInterface $subscriptionInformation
     * @param CustomerInterface $customer
     */
    protected function createSubscriber(SubscriberInformationInterface $subscriptionInformation, CustomerInterface $customer) {}

    /**
     * @param SubscriberInformationInterface $subscriptionInformation
     * @param CustomerInterface $customer
     */
    public function updateSubscriber(SubscriberInformationInterface $subscriptionInformation, CustomerInterface $customer) {
        $subscriberEmail = $subscriptionInformation->getSubscriberEmail();
        if ($this->subscriberExists($subscriberEmail)) {
            $this->updateSubscriberInformations($subscriptionInformation, $customer);
        } else {
            $this->createSubscriber($subscriptionInformation, $customer);
        }
    }

    /**
     * @param SubscriberInformationInterface $subscriptionInformation
     */
    public function deleteSubscription(SubscriberInformationInterface $subscriptionInformation) {}
}
