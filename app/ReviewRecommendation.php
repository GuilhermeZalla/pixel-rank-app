<?php

namespace App;

enum ReviewRecommendation: string
{
    case Essential = 'essential';
    case Recommended = 'recommended';
    case Mixed = 'mixed';
    case Not_Recommended = 'not_recommended';

   public function label(){
     return match($this){
        self::Essential => 'Essential',
        self::Recommended => 'Recommended',
        self::Mixed => 'Mixed',
        self::Not_Recommended => 'Not Recommended'
    };
   }
}
