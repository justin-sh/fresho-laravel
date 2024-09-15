<?php

namespace App;

enum ProductCategory: string
{
    case Beef = "BEEF";
    case Lamb = "LAMB";
    case Pork = "PORK";
    case Chicken = "CHICKEN";
    case Duck = "DUCK";
    case Others = "OTHERS";
}
