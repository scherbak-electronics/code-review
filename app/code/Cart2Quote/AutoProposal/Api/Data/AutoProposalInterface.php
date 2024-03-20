<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Api\Data;

/**
 * Interface AutoProposalInterface
 *
 * @package Cart2Quote\AutoProposal\Api\Data
 */
interface AutoProposalInterface
{
    /**
     * Send notify salesrep email
     */
    const SEND_NOTIFY_SALESREP_EMAIL = 'send_notify_salesrep_email';
    /**
     * Notify salesrep email sent
     */
    const NOTIFY_SALESREP_EMAIL_SENT = 'notify_salesrep_email_sent';
}
