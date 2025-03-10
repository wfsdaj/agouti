<?php

namespace App\Controllers\Admin;

use Hleb\Scheme\App\Controllers\MainController;
use App\Models\User\{InvitationModel, UserModel};
use Base, Translate;

class InvitationsController extends MainController
{
    public function index($sheet)
    {
        $invite = InvitationModel::get();

        $result = [];
        foreach ($invite  as $ind => $row) {
            $row['uid']         = UserModel::getUser($row['uid'], 'id');
            $row['active_time'] = $row['active_time'];
            $result[$ind]       = $row;
        }

        return view(
            '/admin/invitation/invitations',
            [
                'meta'  => meta($m = [], Translate::get('invites')),
                'uid'   => Base::getUid(),
                'data'  => [
                    'sheet'         => $sheet == 'all' ? 'invitations' : $sheet,
                    'invitations'   => $result,
                ]
            ]
        );
    }
}
