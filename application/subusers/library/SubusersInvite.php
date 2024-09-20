<?php

class SubusersInvite extends Am_Record {}

class SubusersInviteTable extends Am_Table
{
    protected $_key = 'subusers_invite_id';
    protected $_table = '?_subusers_invite';
    protected $_recordClass = 'SubusersInvite';

    function invite(User $parent, User $user, $product_ids)
    {
        $this->_db->query(<<<CUT
            INSERT INTO ?_subusers_invite SET ?a
                ON DUPLICATE KEY UPDATE
                    dattm = VALUES(dattm),
                    product_ids = VALUES(product_ids);
CUT
            , [
                'user_id' => $user->pk(),
                'parent_id' => $parent->pk(),
                'product_ids' => implode(',', $product_ids),
                'dattm' => sqlTime('now')
            ]);
    }
}