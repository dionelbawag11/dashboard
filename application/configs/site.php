<?php

if (!defined('INCLUDED_AMEMBER_CONFIG'))
    die("Direct access to this location is not allowed");

//add new block at position member/main/left
Am_Di::getInstance()->blocks->remove('member-main-subscriptions');
Am_Di::getInstance()->blocks->remove('member-main-links');

Am_Di::getInstance()->blocks->add('member/main/right', new Am_Widget_ActiveSubscriptions, 200);
Am_Di::getInstance()->blocks->add('member/main/left', new Am_Widget_MemberLinks, 200);