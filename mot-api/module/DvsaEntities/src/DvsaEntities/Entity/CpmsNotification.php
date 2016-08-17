<?php
/**
 * This file is part of the DVSA MOT API project.
 *
 * @link http://gitlab.clb.npm/mot/mot
 */

namespace DvsaEntities\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DvsaEntities\EntityTrait\CommonIdentityTrait;

/**
 * CpmsNotification entity.
 *
 * @ORM\Entity(repositoryClass="DvsaEntities\Repository\CpmsNotificationRepository")
 * @ORM\Table(name="cpms_notification")
 */
class CpmsNotification extends Entity
{
    use CommonIdentityTrait;

    /**
     * This ID is generated by CPMS, and is independent of any message ID generated by the underlying queueing software.
     * It is guaranteed to be unique.
     *
     * @var string
     *
     * @ORM\Column(name="notification_id", type="string", length=36, nullable=false, unique=true)
     */
    private $notificationId;

    /**
     * Notification type is currently mandate or payment.
     *
     * @var \DvsaEntities\Entity\CpmsNotificationType
     *
     * @ORM\ManyToOne(targetEntity="DvsaEntities\Entity\CpmsNotificationType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $notificationType;

    /**
     * Payment Scopes is the CPMS term for the different types of payment that are supported by MOT et al.
     *
     * @var \DvsaEntities\Entity\CpmsNotificationScope
     *
     * @ORM\ManyToOne(targetEntity="DvsaEntities\Entity\CpmsNotificationScope")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="scope_id", referencedColumnName="id")
     * })
     */
    private $scope;

    /**
     * This is a status internal to us which stores whether the notification has been processed by us.
     *
     * @var \DvsaEntities\Entity\CpmsNotificationStatus
     *
     * @ORM\ManyToOne(targetEntity="DvsaEntities\Entity\CpmsNotificationStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    private $status;

    /**
     * The unique ID of this payment.
     *
     * @var string
     *
     * @ORM\Column(name="receipt_reference", type="string", length=32)
     */
    private $receiptReference;

    /**
     * If the payment scope is direct debit, this will contain the reference to the associated mandate.
     *
     * @var string
     *
     * @ORM\Column(name="mandate_reference", type="string", length=32)
     */
    private $mandateReference;

    /**
     * The raw JSON payload from CPMS.
     *
     * @var string
     *
     * @ORM\Column(name="raw_notification", type="string", nullable=false)
     */
    private $rawNotification;

    /**
     * @var integer
     *
     * @ORM\Column(name="received_count", type="integer", nullable=false)
     */
    private $receivedCount;

    /**
     * @return string
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * @param string $notificationId
     */
    public function setNotificationId($notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * @return CpmsNotificationType
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }

    /**
     * @param CpmsNotificationType $notificationType
     */
    public function setNotificationType(CpmsNotificationType $notificationType)
    {
        $this->notificationType = $notificationType;
    }

    /**
     * @return CpmsNotificationScope
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param CpmsNotificationScope $scope
     */
    public function setScope(CpmsNotificationScope $scope)
    {
        $this->scope = $scope;
    }

    /**
     * @return CpmsNotificationStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param CpmsNotificationStatus $status
     */
    public function setStatus(CpmsNotificationStatus $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getReceiptReference()
    {
        return $this->receiptReference;
    }

    /**
     * @param string $receiptReference
     */
    public function setReceiptReference($receiptReference)
    {
        $this->receiptReference = $receiptReference;
    }

    /**
     * @return string
     */
    public function getMandateReference()
    {
        return $this->mandateReference;
    }

    /**
     * @param string $mandateReference
     */
    public function setMandateReference($mandateReference)
    {
        $this->mandateReference = $mandateReference;
    }

    /**
     * @return string
     */
    public function getRawNotification()
    {
        return $this->rawNotification;
    }

    /**
     * @param string $rawNotification
     */
    public function setRawNotification($rawNotification)
    {
        $this->rawNotification = $rawNotification;
    }

    /**
     * @return int
     */
    public function getReceivedCount()
    {
        return $this->receivedCount;
    }

    /**
     * @param int $receivedCount
     */
    public function setReceivedCount($receivedCount)
    {
        $this->receivedCount = $receivedCount;
    }
}