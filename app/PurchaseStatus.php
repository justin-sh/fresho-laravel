<?php

namespace App;

enum PurchaseStatus:string
{
    case INIT = 'INIT';
    case APPROVE = 'APPROVE';
}
