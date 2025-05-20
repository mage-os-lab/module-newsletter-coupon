<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Plugin;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\ScopeInterface;
use MageOS\NewsletterCoupon\Helper\ConfigurationHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use MageOS\NewsletterCoupon\Api\SubscriberInformationRepositoryInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use MageOS\NewsletterCoupon\Model\SubscriberInformation;

class UpdateNewsletterEmailSending
{

    const XML_PATH_SUCCESS_COUPON_EMAIL_TEMPLATE = 'newsletter/subscription/success_coupon_email_template';

    /**
     * @var ConfigurationHelper
     */
    protected $configurationHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var SubscriberInformationRepositoryInterface
     */
    protected $subscriberInformationRepository;

    /**
     * @var CouponRepositoryInterface
     */
    protected $couponRepository;

    /**
     * UpdateNewsletterEmailSending constructor.
     * @param ConfigurationHelper $configurationHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param SubscriberInformationRepositoryInterface $subscriberInformationRepository
     * @param CouponRepositoryInterface $couponRepository
     */
    public function __construct(
        ConfigurationHelper $configurationHelper,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        SubscriberInformationRepositoryInterface $subscriberInformationRepository,
        CouponRepositoryInterface $couponRepository
    )
    {
        $this->configurationHelper = $configurationHelper;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->subscriberInformationRepository = $subscriberInformationRepository;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @param Subscriber $subject
     * @param callable $proceed
     * @return Subscriber
     */
    public function aroundSendConfirmationRequestEmail(Subscriber $subject, callable $proceed): Subscriber
    {
        if ($this->configurationHelper->disableConfirmationRequestEmail()) {
            return $subject;
        }
        return $proceed();
    }

    /**
     * @param Subscriber $subject
     * @param callable $proceed
     * @return Subscriber
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function aroundSendConfirmationSuccessEmail(Subscriber $subject, callable $proceed): Subscriber
    {
        if ($this->configurationHelper->disableConfirmationSuccessEmail()) {
            return $subject;
        }

        if ($this->configurationHelper->isCouponGenerationEnabled()) {

            if ($subject->getImportMode()) {
                return $proceed();
            }

            if (!$this->scopeConfig->getValue(
                    self::XML_PATH_SUCCESS_COUPON_EMAIL_TEMPLATE,
                    ScopeInterface::SCOPE_STORE
                ) || !$this->scopeConfig->getValue(
                    Subscriber::XML_PATH_SUCCESS_EMAIL_IDENTITY,
                    ScopeInterface::SCOPE_STORE
                )
            ) {
                return $proceed();
            }

            /**
             * @var SubscriberInformation $subscriberInformation
             */
            $subscriberInformation = $this->subscriberInformationRepository->getBySubscriberId($subject->getSubscriberId());

            if ($subscriberInformation && $subscriberInformation->getId() && $subscriberInformation->getCouponId() !== null) {
                $coupon = $this->couponRepository->getById($subscriberInformation->getCouponId());
                $creationDate = \DateTime::createFromFormat(DateTime::DATETIME_PHP_FORMAT, $subscriberInformation->getCreatedAt());
                $expirationDelay = date_interval_create_from_date_string($this->configurationHelper->getDelayExpression());
                $creationDate->add($expirationDelay);
                $expirationDate = $creationDate->format(DateTime::DATETIME_PHP_FORMAT);

                $this->inlineTranslation->suspend();

                $this->transportBuilder->setTemplateIdentifier(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_SUCCESS_COUPON_EMAIL_TEMPLATE,
                        ScopeInterface::SCOPE_STORE
                    )
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId(),
                    ]
                )->setTemplateVars(
                    [
                        'subscriber' => $subject,
                        'coupon_code' => $coupon->getCode(),
                        'coupon_expiration_date' => $expirationDate
                    ]
                )->setFrom(
                    $this->scopeConfig->getValue(
                        Subscriber::XML_PATH_SUCCESS_EMAIL_IDENTITY,
                        ScopeInterface::SCOPE_STORE
                    )
                )->addTo(
                    $subject->getEmail(),
                    $subject->getName()
                );
                $transport = $this->transportBuilder->getTransport();
                $transport->sendMessage();

                $this->inlineTranslation->resume();

                return $subject;

            }
        }

        return $proceed();
    }

    /**
     * @param Subscriber $subject
     * @param callable $proceed
     * @return Subscriber
     */
    public function aroundSendUnsubscriptionEmail(Subscriber $subject, callable $proceed): Subscriber
    {
        if ($this->configurationHelper->disableUnsubscriptionEmail()) {
            return $subject;
        }
        return $proceed();
    }
}
