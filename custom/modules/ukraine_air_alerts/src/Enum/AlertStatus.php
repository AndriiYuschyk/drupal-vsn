<?php


namespace Drupal\ukraine_air_alerts\Enum;

enum AlertStatus: string
{
    case FULL = 'full';
    case PARTIAL  = 'partial';
    case NONE  = 'none';
}