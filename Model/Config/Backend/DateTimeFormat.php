<?php
declare(strict_types=1);

namespace MageOS\NewsletterCoupon\Model\Config\Backend;

use Magento\Framework\App\Config\Value;

class DateTimeFormat extends Value {

    /**
     * @return DateTimeFormat|Value
     */
    public function beforeSave(): DateTimeFormat|Value
    {
        $dataSaveAllowed = false;
        $value = $this->getValue();
        if (strtotime((string)$value) !== false) {
            $dataSaveAllowed = true;
        }
        if (!$dataSaveAllowed) {
            $value = (string)$this->getOldValue();
        }
        $this->setValue((string)$value);
        return parent::beforeSave();
    }
}
