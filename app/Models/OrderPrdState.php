<?php

namespace App\Models;

enum OrderPrdState: string
{
    //all in_progress submitted accepted invoiced paid cancelled
    case ToPick = "to_pick";
    case Supplied = "supplied";
    case PartiallySupplied = "partially_picked";
    case NotAvailable = "n/a";
    case BackOrder = "backorder";
    case Substituted = "substituted";
}
