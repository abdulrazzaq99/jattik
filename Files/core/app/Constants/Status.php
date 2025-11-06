<?php

namespace App\Constants;

class Status{

    const ENABLE = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    CONST TICKET_OPEN = 0;
    CONST TICKET_ANSWER = 1;
    CONST TICKET_REPLY = 2;
    CONST TICKET_CLOSE = 3;

    CONST PRIORITY_LOW = 1;
    CONST PRIORITY_MEDIUM = 2;
    CONST PRIORITY_HIGH = 3;

    const ACTIVE_USER = 1;
	const BAN_USER = 0;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM = 3;

    const SUPER_ADMIN_ID=1;

    const COURIER_QUEUE = 0;
	const COURIER_DISPATCH = 1;
	const COURIER_UPCOMING = 1;
	const COURIER_DELIVERYQUEUE = 2;
	const COURIER_DELIVERED = 3;

    const PAID = 1;
	const UNPAID = 0;
}
