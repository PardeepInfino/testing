<?php
/**
 * Created by PhpStorm.
 * User: ENEXUM - CMOLINA
 * Date: 17/01/2018
 * Time: 10:50
 */

namespace TMWK\ClientPrestashopApi\Doc;


class CustomerDetails
{
    public $id;

    public $id_default_group;

    public $id_lang;

    public $newsletter_date_add;

    public $ip_registration_newsletter;

    public $last_passwd_gen;

    public $secure_key;

    public $deleted;

    public $passwd;

    public $lastname;

    public $firstname;

    public $email;

    public $card_code;

    public $id_gender;

    public $birthday;

    public $newsletter;

    public $optin;

    public $website;

    public $company;

    public $siret;

    public $ape;

    public $outstanding_allow_amount;

    public $show_public_prices;

    public $id_risk;

    public $max_payment_days;

    public $active;

    public $note;

    public $is_guest;

    public $id_shop;

    public $id_shop_group;

    public $date_add;

    public $date_upd;

    /**
     * @var CustomnerAssociations
     */
    public $associations;
}