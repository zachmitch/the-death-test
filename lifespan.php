<?php
  function remainingLife($currentAge, $avgLife){
      $currentAge = (float)$currentAge;
      $avgLife = (float)$avgLife;
      $remainingDays = 0;
      $percent_lived = $currentAge/$avgLife;


      switch (true) {
          case ($percent_lived >= 1.269035533):
            $remainingDays = 0.029187817 * $avgLife;
            break;
          case ($percent_lived >= 1.205583756):
            $remainingDays = 0.040609137 * $avgLife;
            break;
          case ($percent_lived >= 1.14213198):
            $remainingDays = 0.058375635 * $avgLife;
            break;
          case ($percent_lived >= 1.078680203):
            $remainingDays = 0.083756345 * $avgLife;
            break;
          case ($percent_lived >= 1.015228426):
            $remainingDays = 0.116751269 * $avgLife;
            break;
          case ($percent_lived >= 0.95177665):
            $remainingDays = 0.154822335 * $avgLife;
            break;
          case ($percent_lived >= 0.888324873):
            $remainingDays = 0.197969543 * $avgLife;
            break;
          case ($percent_lived >= 0.824873096):
            $remainingDays = 0.244923858 * $avgLife;
            break;
          case ($percent_lived >= 0.76142132):
            $remainingDays = 0.295685279 * $avgLife;
            break;
          case ($percent_lived >= 0.697969543):
            $remainingDays = 0.346446701 * $avgLife;
            break;
          case ($percent_lived >= 0.634517766):
            $remainingDays = 0.401015228 * $avgLife;
            break;
          case ($percent_lived >= 0.57106599):
            $remainingDays = 0.458121827 * $avgLife;
            break;
          case ($percent_lived >= 0.507614213):
            $remainingDays = 0.516497462 * $avgLife;
            break;
          case ($percent_lived >= 0.444162437):
            $remainingDays = 0.576142132 * $avgLife;
            break;
          case ($percent_lived >= 0.38071066):
            $remainingDays = 0.635786802 * $avgLife;
            break;
          case ($percent_lived >= 0.317258883):
            $remainingDays = 0.695431472 * $avgLife;
            break;
          case ($percent_lived >= 0.253807107):
            $remainingDays = 0.756345178 * $avgLife;
            break;
          case ($percent_lived >= 0.19035533):
            $remainingDays = 0.818527919 * $avgLife;
            break;
          case ($percent_lived >= 0.126903553):
            $remainingDays = 0.88071066 * $avgLife;
            break;
          case ($percent_lived >= 0.063451777):
            $remainingDays = 0.944162437 * $avgLife;
            break;
          case ($percent_lived >= 0.012690355):
            $remainingDays = 0.993654822 * $avgLife;
            break;
          default:
            $remainingDays = 1 * $avgLife;
            break;
      }

  return (int)$remainingDays;
}
?>