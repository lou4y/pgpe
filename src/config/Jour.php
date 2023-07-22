<?php

namespace App\config;
use phpDocumentor\Reflection\Types\Integer;

enum Jour: string
{
    case Lundi = "lundi";
    case Mardi = "mardi";
    case Mercredi = "mercredi";
    case Jeudi = "jeudi";
    case Vendredi = "vendredi";
    case Samedi = "samedi";
    case Dimanche = "dimanche";

}

