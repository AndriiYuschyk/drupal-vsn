<?php


namespace Drupal\ukraine_air_alerts\Enum;

enum Regions: string
{
    case AutonomousRepublicOfCrimea = 'Автономна Республіка Крим';
    case Volyn = 'Волинська область';
    case Vinnytsia = 'Вінницька область';
    case Dnipro = 'Дніпропетровська область';
    case Donetsk = 'Донецька область';
    case Zhytomyr = 'Житомирська область';
    case Zakarpattia = 'Закарпатська область';
    case Zaporizhzhia = 'Запорізька область';
    case IvanoFrankivsk = 'Івано-Франківська область';
    case KyivCity = 'м. Київ';
    case Kyiv = 'Київська область';
    case Kirovohrad = 'Кіровоградська область';
    case Luhansk = 'Луганська область';
    case Lviv = 'Львівська область';
    case Mykolaiv = 'Миколаївська область';
    case Odesa = 'Одеська область';
    case Poltava = 'Полтавська область';
    case Rivne = 'Рівненська область';
    case SevastopolCity = 'м. Севастополь';
    case Sumy = 'Сумська область';
    case Ternopil = 'Тернопільська область';
    case Kharkiv = 'Харківська область';
    case Kherson = 'Херсонська область';
    case Khmelnytskyi = 'Хмельницька область';
    case Cherkasy = 'Черкаська область';
    case Chernivtsi = 'Чернівецька область';
    case Chernihiv = 'Чернігівська область';

    /**
     * Returns an array of all region names.
     *
     * @return array
     */
    public static function getAllRegions(): array
    {
        return array_column(self::cases(), 'value');
    }
}