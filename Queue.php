<?php

namespace go1\util;

class Queue
{
    # The entity events
    # -------
    const LO_CREATE        = 'lo.create'; # Body: LO object, no lo.items should be expected.
    const LO_UPDATE        = 'lo.update'; # Body: LO object with extra property: origin.
    const LO_DELETE        = 'lo.delete'; # Body: LO object.
    const USER_CREATE      = 'user.create';
    const USER_UPDATE      = 'user.update';
    const RO_CREATE        = 'ro.create';
    const RO_UPDATE        = 'ro.update';
    const RO_DELETE        = 'ro.delete';
    const VOTE_CREATE      = 'vote.create';
    const VOTE_UPDATE      = 'vote.update';
    const VOTE_DELETE      = 'vote.delete';
    const CUSTOMER_CREATE  = 'customer.create';
    const CUSTOMER_UPDATE  = 'customer.update';
    const CUSTOMER_DELETE  = 'customer.delete';
    const ENROLMENT_CREATE = 'enrolment.create';
    const ENROLMENT_UPDATE = 'enrolment.update';
    const ENROLMENT_DELETE = 'enrolment.delete';
    const PORTAL_CREATE    = 'portal.create';
    const PORTAL_UPDATE    = 'portal.update';
    const PORTAL_DELETE    = 'portal.delete';
    const TAG_CREATE       = 'tag.create';
    const TAG_UPDATE       = 'tag.update';
    const TAG_DELETE       = 'tag.delete';

    # routingKey that tell some service do something.
    # -------
    const DO_CONSUMER_HTTP_REQUEST = 'do.consumer.HttpRequest'; # { method: STRING, url: STRING, query: STRING, headers: map[STRING][STRING], body: STRING }
}
