<?php

namespace App;

enum Usertype: string
{
    case SUPERADMIN = 'Super Admin';
    case FRONTDESK = 'Front-Desk';
    case LAB = 'Lab';
    case CUSTOMER = 'Customer';
    case PHARM = 'Pharm';
    case NURSE = 'Nurse';
}
