<?php
/**
 * Created by PhpStorm.
 * User: ENEXUM - CMOLINA
 * Date: 17/01/2018
 * Time: 14:19
 */

namespace TMWK\ClientPrestashopApi\Entities;


use TMWK\ClientPrestashopApi\Config;
use TMWK\ClientPrestashopApi\PrestaShopWebService;

class Customers
{
    public function __construct($id = null)
    {
        if ($id !== null) {
            $es = new PrestaShopWebService(Config::getUrl(), Config::getKey(), Config::getDebug());

            $entity                           = $es->Customers()->find($id);
            $this->id                         = $entity->customer->id;
            $this->id_lang                    = $entity->customer->id_lang;
            $this->newsletter_date_add        = $entity->customer->newsletter_date_add;
            $this->ip_registration_newsletter = $entity->customer->ip_registration_newsletter;
            $this->last_passwd_gen            = $entity->customer->last_passwd_gen;
            $this->secure_key                 = $entity->customer->secure_key;
            $this->deleted                    = $entity->customer->deleted;
            $this->passwd                     = $entity->customer->passwd;
            $this->lastname                   = $entity->customer->lastname;
            $this->firstname                  = $entity->customer->firstname;
            $this->email                      = $entity->customer->email;
            $this->card_code                  = $entity->customer->card_code;
            $this->id_gender                  = $entity->customer->id_gender;
            $this->birthday                   = $entity->customer->birthday;
            $this->newsletter                 = $entity->customer->newsletter;
            $this->optin                      = $entity->customer->optin;
            $this->website                    = $entity->customer->website;
            $this->company                    = $entity->customer->company;
            $this->siret                      = $entity->customer->siret;
            $this->ape                        = $entity->customer->ape;
            $this->outstanding_allow_amount   = $entity->customer->outstanding_allow_amount;
            $this->show_public_prices         = $entity->customer->show_public_prices;
            $this->id_risk                    = $entity->customer->id_risk;
            $this->max_payment_days           = $entity->customer->max_payment_days;
            $this->active                     = $entity->customer->active;
            $this->note                       = $entity->customer->note;
            $this->is_guest                   = $entity->customer->is_guest;
            $this->id_shop                    = $entity->customer->id_shop;
            $this->id_shop_group              = $entity->customer->id_shop_group;
            $this->date_add                   = $entity->customer->date_add;
            $this->date_upd                   = $entity->customer->date_upd;

            foreach ($entity->customer->associations->groups->group as $group) {
                $this->groups[]['id'] = $group->id;
            }


        }
    }

    private $id;

    private $id_default_group;

    private $id_lang = 1;

    private $newsletter_date_add = '0000-00-00 00:00:00';

    private $ip_registration_newsletter = '127.0.0.1';

    private $last_passwd_gen = '0000-00-00 00:00:00';

    private $secure_key;

    private $deleted = 0;

    private $passwd;

    private $lastname;

    private $firstname;

    private $email;

    private $card_code;

    private $id_gender;

    private $birthday = '0000-00-00';

    private $newsletter = 0;

    private $optin = 0;

    private $website;

    private $company;

    private $siret;

    private $ape;

    private $outstanding_allow_amount = 0;

    private $show_public_prices = 0;

    private $id_risk = 1;

    private $max_payment_days = 0;

    private $active = 1;

    private $note;

    private $is_guest = 0;

    private $id_shop = 1;

    private $id_shop_group = 1;

    private $date_add = '0000-00-00 00:00:00';

    private $date_upd = '0000-00-00 00:00:00';

    private $groups = array();

    private $init = false;

    /**
     * @return \SimpleXMLElement
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $id_default_group
     */
    public function setIdDefaultGroup($id_default_group)
    {
        $this->id_default_group = $id_default_group;
    }

    /**
     * @param mixed $id_lang
     */
    public function setIdLang($id_lang)
    {
        $this->id_lang = $id_lang;
    }

    /**
     * @param mixed $newsletter_date_add
     */
    public function setNewsletterDateAdd($newsletter_date_add)
    {
        $this->newsletter_date_add = $newsletter_date_add;
    }

    /**
     * @param mixed $ip_registration_newsletter
     */
    public function setIpRegistrationNewsletter($ip_registration_newsletter)
    {
        $this->ip_registration_newsletter = $ip_registration_newsletter;
    }

    /**
     * @param mixed $last_passwd_gen
     */
    public function setLastPasswdGen($last_passwd_gen)
    {
        $this->last_passwd_gen = $last_passwd_gen;
    }

    /**
     * @param mixed $secure_key
     */
    public function setSecureKey($secure_key)
    {
        $this->secure_key = $secure_key;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @param mixed $passwd
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $card_code
     */
    public function setCardCode($card_code)
    {
        $this->card_code = $card_code;
    }

    /**
     * @param mixed $id_gender
     */
    public function setIdGender($id_gender)
    {
        $this->id_gender = $id_gender;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @param mixed $newsletter
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @param mixed $optin
     */
    public function setOptin($optin)
    {
        $this->optin = $optin;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @param mixed $siret
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;
    }

    /**
     * @param mixed $ape
     */
    public function setApe($ape)
    {
        $this->ape = $ape;
    }

    /**
     * @param mixed $outstanding_allow_amount
     */
    public function setOutstandingAllowAmount($outstanding_allow_amount)
    {
        $this->outstanding_allow_amount = $outstanding_allow_amount;
    }

    /**
     * @param mixed $show_public_prices
     */
    public function setShowPublicPrices($show_public_prices)
    {
        $this->show_public_prices = $show_public_prices;
    }

    /**
     * @param mixed $id_risk
     */
    public function setIdRisk($id_risk)
    {
        $this->id_risk = $id_risk;
    }

    /**
     * @param mixed $max_payment_days
     */
    public function setMaxPaymentDays($max_payment_days)
    {
        $this->max_payment_days = $max_payment_days;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @param mixed $is_guest
     */
    public function setIsGuest($is_guest)
    {
        $this->is_guest = $is_guest;
    }

    /**
     * @param mixed $id_shop
     */
    public function setIdShop($id_shop)
    {
        $this->id_shop = $id_shop;
    }

    /**
     * @param mixed $id_shop_group
     */
    public function setIdShopGroup($id_shop_group)
    {
        $this->id_shop_group = $id_shop_group;
    }

    /**
     * @param mixed $date_add
     */
    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    /**
     * @param mixed $date_upd
     */
    public function setDateUpd($date_upd)
    {
        $this->date_upd = $date_upd;
    }

    /**
     * @param integer $group_id
     */
    public function setGroup($group_id)
    {
        if($this->init === false){
            $this->groups = array();
            $this->init = true;
        }
        $this->groups[] = array(
            'group' => array('id' => $group_id)
        );
    }

    public function getParameters()
    {
        return array(
            'id'                         => (string)$this->id,
            'id_default_group'           => (string)$this->id_default_group,
            'id_lang'                    => (string)$this->id_lang,
            'newsletter_date_add'        => (string)$this->newsletter_date_add,
            'ip_registration_newsletter' => (string)$this->ip_registration_newsletter,
            'last_passwd_gen'            => (string)$this->last_passwd_gen,
            'secure_key'                 => (string)$this->secure_key,
            'deleted'                    => (string)$this->deleted,
            'passwd'                     => (string)$this->passwd,
            'lastname'                   => (string)$this->lastname,
            'firstname'                  => (string)$this->firstname,
            'email'                      => (string)$this->email,
            'card_code'                  => (string)$this->card_code,
            'id_gender'                  => (string)$this->id_gender,
            'birthday'                   => (string)$this->birthday,
            'newsletter'                 => (string)$this->newsletter,
            'optin'                      => (string)$this->optin,
            'website'                    => (string)$this->website,
            'company'                    => (string)$this->company,
            'siret'                      => (string)$this->siret,
            'ape'                        => (string)$this->ape,
            'outstanding_allow_amount'   => (string)$this->outstanding_allow_amount,
            'show_public_prices'         => (string)$this->show_public_prices,
            'id_risk'                    => (string)$this->id_risk,
            'max_payment_days'           => (string)$this->max_payment_days,
            'active'                     => (string)$this->active,
            'note'                       => (string)$this->note,
            'is_guest'                   => (string)$this->is_guest,
            'id_shop'                    => (string)$this->id_shop,
            'id_shop_group'              => (string)$this->id_shop_group,
            'date_add'                   => (string)$this->date_add,
            'date_upd'                   => (string)$this->date_upd,
            'associations'               => array(
                'groups' => $this->groups
            ),
        );
    }

}