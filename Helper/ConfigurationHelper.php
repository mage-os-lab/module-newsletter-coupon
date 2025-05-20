<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigurationHelper
{
    /**
     * Module section
     */
    const SECTION_MODULE = 'mageos_newsletter';
    /**
     * Configuration section
     */
    const GROUP_CONFIGURATION = self::SECTION_MODULE . '/configuration';
    /**
     * Enabled feature config path
     */
    const PATH_COUPON_GENERATION_ENABLED = self::GROUP_CONFIGURATION . '/coupon_generation_enabled';
    /**
     * Sales rule used for coupon generation config path
     */
    const PATH_USED_SALESRULE_ID = self::GROUP_CONFIGURATION . '/salesrule_id';
    /**
     * Coupon expiration delay expression config path
     */
    const PATH_COUPON_EXPIRATION_DELAY_EXPRESSION = self::GROUP_CONFIGURATION . '/expiration_delay_expression';

    /**
     * Newsletter Module section
     */
    const NEWSLETTER_SECTION_MODULE = 'newsletter';
    /**
     * Configuration section
     */
    const NEWSLETTER_GROUP_CONFIGURATION = self::NEWSLETTER_SECTION_MODULE . '/subscription';
    /**
     * Disable email sending config path
     */
    const PATH_DISABLE_NEWSLETTER_CONFIRMATION_REQUEST_EMAIL = self::NEWSLETTER_GROUP_CONFIGURATION . '/disable_confirmation_request_email';
    /**
     * Disable email sending config path
     */
    const PATH_DISABLE_NEWSLETTER_CONFIRMATION_SUCCESS_EMAIL = self::NEWSLETTER_GROUP_CONFIGURATION . '/disable_confirmation_success_email';
    /**
     * Disable email sending config path
     */
    const PATH_DISABLE_NEWSLETTER_UNSUBSCRIPTION_EMAIL = self::NEWSLETTER_GROUP_CONFIGURATION . '/disable_unsubscription_email';

    /**
     * ConfigurationHelper constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig
    )
    {
    }

    /**
     * @param $store
     * @param string $scope
     * @return bool
     */
    public function isCouponGenerationAvailable($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->getUsedSalesRuleId($store, $scope) && $this->isCouponGenerationEnabled($store, $scope);
    }

    /**
     * @param null $store
     * @param string $scope
     * @return bool
     */
    public function isCouponGenerationEnabled($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_COUPON_GENERATION_ENABLED,
            $scope,
            $store
        );
    }

    /**
     * @param null $store
     * @param string $scope
     * @return mixed
     */
    public function getUsedSalesRuleId($store = null, string $scope = ScopeInterface::SCOPE_STORE): mixed
    {
        return $this->scopeConfig->getValue(
            self::PATH_USED_SALESRULE_ID,
            $scope,
            $store
        );
    }

    /**
     * @param null $store
     * @param string $scope
     * @return mixed
     */
    public function getDelayExpression($store = null, string $scope = ScopeInterface::SCOPE_STORE): mixed
    {
        return $this->scopeConfig->getValue(
            self::PATH_COUPON_EXPIRATION_DELAY_EXPRESSION,
            $scope,
            $store
        );
    }

    /**
     * @param null $store
     * @param string $scope
     * @return mixed
     */
    public function disableConfirmationRequestEmail($store = null, string $scope = ScopeInterface::SCOPE_STORE): mixed
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_DISABLE_NEWSLETTER_CONFIRMATION_REQUEST_EMAIL,
            $scope,
            $store
        );
    }

    /**
     * @param null $store
     * @param string $scope
     * @return mixed
     */
    public function disableConfirmationSuccessEmail($store = null, string $scope = ScopeInterface::SCOPE_STORE): mixed
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_DISABLE_NEWSLETTER_CONFIRMATION_SUCCESS_EMAIL,
            $scope,
            $store
        );
    }

    /**
     * @param null $store
     * @param string $scope
     * @return bool
     */
    public function disableUnsubscriptionEmail($store = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_DISABLE_NEWSLETTER_UNSUBSCRIPTION_EMAIL,
            $scope,
            $store
        );
    }
}
