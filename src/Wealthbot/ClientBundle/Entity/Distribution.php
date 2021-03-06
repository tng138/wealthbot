<?php

namespace Wealthbot\ClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wealthbot\ClientBundle\Model\PaymentWorkflowableInterface;
use Wealthbot\SignatureBundle\Entity\DocumentSignature;
use Wealthbot\SignatureBundle\Model\SignableInterface;

/**
 * Distribution
 */
class Distribution implements SignableInterface, PaymentWorkflowableInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    const TYPE_SCHEDULED = 'scheduled';
    const TYPE_ONE_TIME  = 'one_time';

    static private $_types = array(
        self::TYPE_SCHEDULED => 'scheduled',
        self::TYPE_ONE_TIME  => 'one time'
    );

    /**
     * @var string
     */
    private $transferMethod;

    const TRANSFER_METHOD_RECEIVE_CHECK = 'receive_check';
    const TRANSFER_METHOD_WIRE_TRANSFER = 'wire_transfer';
    const TRANSFER_METHOD_BANK_TRANSFER = 'bank_transfer';
    const TRANSFER_METHOD_NOT_FUNDING   = 'not_funding';

    static private $_transferMethods = null;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var \DateTime
     */
    private $transferDate;

    /**
     * @var integer
     */
    private $frequency;

    //const FREQUENCY_ONE_TIME         = 1;
    const FREQUENCY_EVERY_OTHER_WEEK = 2;
    const FREQUENCY_MONTHLY          = 3;
    const FREQUENCY_QUARTERLY        = 4;

    static private $_frequencies = array(
        //self::FREQUENCY_ONE_TIME         => 'One-time',
        self::FREQUENCY_EVERY_OTHER_WEEK => 'Every other week',
        self::FREQUENCY_MONTHLY          => 'Monthly',
        self::FREQUENCY_QUARTERLY        => 'Quarterly'
    );

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \Wealthbot\ClientBundle\Entity\SystemAccount
     */
    private $systemClientAccount;

    /**
     * @var \Wealthbot\ClientBundle\Entity\BankInformation
     */
    private $bankInformation;

    /**
     * @var integer
     */
    private $distributionMethod;

    const DISTRIBUTION_METHOD_NORMAL    = 1;
    const DISTRIBUTION_METHOD_PREMATURE = 2;

    static private $_distributionMethods = array(
        self::DISTRIBUTION_METHOD_NORMAL => 'normal',
        self::DISTRIBUTION_METHOD_PREMATURE => 'premature'
    );


    /**
     * @var integer
     */
    private $federalWithholding;

    const FEDERAL_WITHHOLDING_TAXES = 1;
    const FEDERAL_WITHHOLDING_NO    = 2;

    static private $_federalWithholding = array(
        self::FEDERAL_WITHHOLDING_TAXES => 'Please withhold taxes from my distribution at a rate of',
        self::FEDERAL_WITHHOLDING_NO => 'I select NOT to have federal income tax withheld'
    );

    /**
     * @var float
     */
    private $federalWithholdMoney;

    /**
     * @var float
     */
    private $federalWithholdPercent;

    /**
     * @var integer
     */
    private $stateWithholding;

    const STATE_WITHHOLDING_TAXES           = 1;
    const STATE_WITHHOLDING_RESIDENCE_STATE = 2;
    const STATE_WITHHOLDING_NO              = 3;

    static private $_stateWithholding = array(
        self::STATE_WITHHOLDING_TAXES => 'Please withhold taxes from my distribution at rate of',
        self::STATE_WITHHOLDING_RESIDENCE_STATE => 'I declare my permanent state of residence is',
        self::STATE_WITHHOLDING_NO => 'I select NOT to have federal income tax withheld'
    );

    /**
     * @var float
     */
    private $stateWithholdMoney;

    /**
     * @var float
     */
    private $stateWithholdPercent;

    /**
     * @var \Wealthbot\AdminBundle\Entity\State
     */
    private $residenceState;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    static public function getTypeChoices()
    {
        return self::$_types;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Distribution
     * @throws \InvalidArgumentException
     */
    public function setType($type)
    {
        if (!array_key_exists($type, self::getTypeChoices())) {
            throw new \InvalidArgumentException(sprintf('Invalid value for type : %s.', $type));
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Is scheduled distribution
     *
     * @return bool
     */
    public function isScheduled()
    {
        return $this->type === self::TYPE_SCHEDULED;
    }

    /**
     * Is one-time distribution
     *
     * @return bool
     */
    public function isOneTime()
    {
        return $this->type === self::TYPE_ONE_TIME;
    }

    /**
     * Get transfer method choices
     *
     * @return array
     */
    static public function getTransferMethodChoices()
    {
        if (self::$_transferMethods === null) {
            self::$_transferMethods = array();
            $oClass = new \ReflectionClass('\Wealthbot\ClientBundle\Entity\Distribution');
            $classConstants = $oClass->getConstants();
            $constantPrefix = "TRANSFER_METHOD_";
            foreach ($classConstants as $key => $val) {
                if (substr($key, 0, strlen($constantPrefix)) === $constantPrefix) {
                    self::$_transferMethods[$val] = $val;
                }
            }
        }

        return self::$_transferMethods;
    }

    /**
     * Set transferMethod
     *
     * @param string $transferMethod
     * @return Distribution
     * @throws \InvalidArgumentException
     */
    public function setTransferMethod($transferMethod)
    {
        if (!in_array($transferMethod, self::getTransferMethodChoices())) {
            throw new \InvalidArgumentException(sprintf('Invalid value for transfer_method : %s.', $transferMethod));
        }

        $this->transferMethod = $transferMethod;

        return $this;
    }

    /**
     * Get transferMethod
     *
     * @return string
     */
    public function getTransferMethod()
    {
        return $this->transferMethod;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Distribution
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set transferDate
     *
     * @param \DateTime $transferDate
     * @return Distribution
     */
    public function setTransferDate($transferDate)
    {
        $this->transferDate = $transferDate;

        return $this;
    }

    /**
     * Get transferDate
     *
     * @return \DateTime
     */
    public function getTransferDate()
    {
        return $this->transferDate;
    }

    /**
     * Get frequency choices
     *
     * @return array
     */
    static public function getFrequencyChoices()
    {
        return self::$_frequencies;
    }

    /**
     * Set frequency
     *
     * @param integer $frequency
     * @return Distribution
     * @throws \InvalidArgumentException
     */
    public function setFrequency($frequency)
    {
        if (null !== $frequency &&  !array_key_exists($frequency, self::getFrequencyChoices())) {
            throw new \InvalidArgumentException(sprintf('Invalid value for frequency : %s.', $frequency));
        }

        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Get frequency
     *
     * @return integer
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Distribution
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Distribution
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set systemClientAccount
     *
     * @param \Wealthbot\ClientBundle\Entity\SystemAccount $systemClientAccount
     * @return Distribution
     */
    public function setSystemClientAccount(\Wealthbot\ClientBundle\Entity\SystemAccount $systemClientAccount = null)
    {
        $this->systemClientAccount = $systemClientAccount;

        return $this;
    }

    /**
     * Get systemClientAccount
     *
     * @return \Wealthbot\ClientBundle\Entity\SystemAccount
     */
    public function getSystemClientAccount()
    {
        return $this->systemClientAccount;
    }

    /**
     * Set bankInformation
     *
     * @param \Wealthbot\ClientBundle\Entity\BankInformation $bankInformation
     * @return Distribution
     */
    public function setBankInformation(\Wealthbot\ClientBundle\Entity\BankInformation $bankInformation = null)
    {
        $this->bankInformation = $bankInformation;

        return $this;
    }

    /**
     * Get bankInformation
     *
     * @return \Wealthbot\ClientBundle\Entity\BankInformation
     */
    public function getBankInformation()
    {
        return $this->bankInformation;
    }

    /**
     * Get distribution_method choices
     *
     * @return array
     */
    static public function getDistributionMethodChoices()
    {
        return self::$_distributionMethods;
    }

    /**
     * Set distributionMethod
     *
     * @param integer $distributionMethod
     * @return Distribution
     * @throws \InvalidArgumentException
     */
    public function setDistributionMethod($distributionMethod)
    {
        if (null !== $distributionMethod &&
            !array_key_exists($distributionMethod, self::getDistributionMethodChoices())
        ) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value for distribution_method : %s.',
                $distributionMethod
            ));
        }

        $this->distributionMethod = $distributionMethod;

        return $this;
    }

    /**
     * Get distributionMethod
     *
     * @return integer
     */
    public function getDistributionMethod()
    {
        return $this->distributionMethod;
    }

    /**
     * Get distribution_method as string
     *
     * @return string
     */
    public function getDistributionMethodAsString()
    {
        if (null === $this->distributionMethod) {
            return '';
        }

        return self::$_distributionMethods[$this->distributionMethod];
    }

    /**
     * Get federal_withholding choices
     *
     * @return array
     */
    static public function getFederalWithholdingChoices()
    {
        return self::$_federalWithholding;
    }

    /**
     * Set federalWithholding
     *
     * @param integer $federalWithholding
     * @return Distribution
     * @throws \InvalidArgumentException
     */
    public function setFederalWithholding($federalWithholding)
    {
        if (null !== $federalWithholding &&
            !array_key_exists($federalWithholding, self::getFederalWithholdingChoices())
        ) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value for federal_withholding : %s.',
                $federalWithholding
            ));
        }

        $this->federalWithholding = $federalWithholding;

        return $this;
    }

    /**
     * Get federalWithholding
     *
     * @return integer
     */
    public function getFederalWithholding()
    {
        return $this->federalWithholding;
    }

    /**
     * Set federalWithholdMoney
     *
     * @param float $federalWithholdMoney
     * @return Distribution
     */
    public function setFederalWithholdMoney($federalWithholdMoney)
    {
        $this->federalWithholdMoney = $federalWithholdMoney;

        return $this;
    }

    /**
     * Get federalWithholdMoney
     *
     * @return float
     */
    public function getFederalWithholdMoney()
    {
        return $this->federalWithholdMoney;
    }

    /**
     * Set federalWithholdPercent
     *
     * @param float $federalWithholdPercent
     * @return Distribution
     */
    public function setFederalWithholdPercent($federalWithholdPercent)
    {
        $this->federalWithholdPercent = $federalWithholdPercent;

        return $this;
    }

    /**
     * Get federalWithholdPercent
     *
     * @return float
     */
    public function getFederalWithholdPercent()
    {
        return $this->federalWithholdPercent;
    }

    /**
     * Get state_withholding choices
     *
     * @return array
     */
    static public function getStateWithholdingChoices()
    {
        return self::$_stateWithholding;
    }

    /**
     * Set stateWithholding
     *
     * @param integer $stateWithholding
     * @return Distribution
     * @throws \InvalidArgumentException
     */
    public function setStateWithholding($stateWithholding)
    {
        if (null !== $stateWithholding &&
            !array_key_exists($stateWithholding, self::getStateWithholdingChoices())
        ) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value for state_withholding : %s.',
                $stateWithholding
            ));
        }

        $this->stateWithholding = $stateWithholding;

        return $this;
    }

    /**
     * Get stateWithholding
     *
     * @return integer
     */
    public function getStateWithholding()
    {
        return $this->stateWithholding;
    }

    /**
     * Set stateWithholdMoney
     *
     * @param float $stateWithholdMoney
     * @return Distribution
     */
    public function setStateWithholdMoney($stateWithholdMoney)
    {
        $this->stateWithholdMoney = $stateWithholdMoney;

        return $this;
    }

    /**
     * Get stateWithholdMoney
     *
     * @return float
     */
    public function getStateWithholdMoney()
    {
        return $this->stateWithholdMoney;
    }

    /**
     * Set stateWithholdPercent
     *
     * @param float $stateWithholdPercent
     * @return Distribution
     */
    public function setStateWithholdPercent($stateWithholdPercent)
    {
        $this->stateWithholdPercent = $stateWithholdPercent;

        return $this;
    }

    /**
     * Get stateWithholdPercent
     *
     * @return float
     */
    public function getStateWithholdPercent()
    {
        return $this->stateWithholdPercent;
    }

    /**
     * Set residenceState
     *
     * @param \Wealthbot\AdminBundle\Entity\State $residenceState
     * @return Distribution
     */
    public function setResidenceState(\Wealthbot\AdminBundle\Entity\State $residenceState = null)
    {
        $this->residenceState = $residenceState;

        return $this;
    }

    /**
     * Get residenceState
     *
     * @return \Wealthbot\AdminBundle\Entity\State
     */
    public function getResidenceState()
    {
        return $this->residenceState;
    }

    /**
     * Get client account object
     *
     * @return \Wealthbot\ClientBundle\Model\ClientAccount
     */
    public function getClientAccount()
    {
        return $this->systemClientAccount ? $this->systemClientAccount->getClientAccount() : null;
    }

    /**
     * Get id of source object
     *
     * @return mixed
     */
    public function getSourceObjectId()
    {
        return $this->id;
    }

    /**
     * Get type of document signature
     *
     * @return string
     */
    public function getDocumentSignatureType()
    {
        if ($this->type === self::TYPE_SCHEDULED) {
            $signatureType = DocumentSignature::TYPE_AUTO_DISTRIBUTION;
        } else {
            $signatureType = DocumentSignature::TYPE_ONE_TIME_DISTRIBUTION;
        }

        return $signatureType;
    }

    /**
     * Get workflow message code
     *
     * @return string
     */
    public function getWorkflowMessageCode()
    {
        return Workflow::MESSAGE_CODE_PAPERWORK_UPDATE_DISTRIBUTIONS;
    }

    /**
     * Get workflow amount
     *
     * @return float
     */
    public function getWorkflowAmount()
    {
        return $this->getAmount();
    }


}
