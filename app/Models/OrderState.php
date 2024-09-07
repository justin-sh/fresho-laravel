<?php

namespace App\Models;

enum OrderState: string
{
    //all in_progress submitted accepted invoiced paid cancelled
    case All = "all";
    case InProgress = "in_progress";
    case Submitted = "submitted";
    case Accepted = "accepted";
    case Invoiced = "invoiced";
    case Paid = "paid";
    case Cancelled = "cancelled";
}
