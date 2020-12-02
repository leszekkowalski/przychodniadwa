<?php
declare(strict_types=1);

namespace Application\Validator;

use Laminas\Validator\AbstractValidator;

class PeselValidator extends AbstractValidator {
    
    const FATAL_ERROR  = 'fatalError';
    const FATAL_DLUDOSC='fatalDlugosc';
    const NUMERIC_ERROR='numericError';
    
    protected $pesel;
    protected $dlugosc=11;
    
    protected $messageTemplates = [
    self::FATAL_ERROR  => "Niepoprawny format numeru PESEL",
    self::FATAL_DLUDOSC=>"Długość numeru PESEL jest niepoprawna. Powinna wynosić 11 cyfr",
    self::NUMERIC_ERROR=>"PESEL powinien składac się wyłącznie z 11 cyfr"
  ];
    
    public function isValid($pesel): bool {
        $this->setPesel($pesel);
        
        if(!strlen($this->pesel)==$this->dlugosc){
            $this->error(self::FATAL_DLUDOSC);
              return false; // Dłogość Pesel jest niepoprawna
        }else{
            
            if (!preg_match('/^[0-9]{11}$/', $this->pesel)) {
            $this->error(self::NUMERIC_ERROR);
            return false;
        }else{
                if ($this->checkSum() && $this->checkMonth() && $this->checkDay()) {
                return true;// Pesel jest poprawny
                    }
                    else {
                     $this->error(self::FATAL_ERROR);   
                    return false;// Składnia  Pesel jest niepoprawna
                    
                    }
            }
        }

}

public function setPesel($pesel){
        $this->pesel=$pesel;
    } 
////////////////////////////////////////////////   
 private function getBirthYear() : int {
 $year=0;
 $month=0;
$year = 10 * ($this->pesel[0]);
$year += $this->pesel[1];
$month = 10 * $this->pesel[2];
$month += $this->pesel[3];
if ($month > 80 && $month < 93) {
$year += 1800;
}
else if ($month > 0 && $month < 13) {
$year += 1900;
}
else if ($month > 20 && $month < 33) {
$year += 2000;
}
else if ($month > 40 && $month < 53) {
$year += 2100;
}
else if ($month > 60 && $month < 73) {
$year += 2200;
}
return (int)$year;
}   
 ///////////////////////   
private function getBirthMonth() {

$month = 10 * $this->pesel[2];
$month += $this->pesel[3];
if ($month > 80 && $month < 93) {
$month -= 80;
}
else if ($month > 20 && $month < 33) {
$month -= 20;
}
else if ($month > 40 && $month < 53) {
$month -= 40;
}
else if ($month > 60 && $month < 73) {
$month -= 60;
}
return $month;
} 
//////////////////////////////////////////////
private function getBirthDay() {
$day=0;
$day = 10 * $this->pesel[4];
$day += $this->pesel[5];
return $day;
}
/////////////////////////////////////////////////
public function getGender() {
if ($this->isValid($this->pesel)) {
if ($this->pesel[9] % 2 == 1) {
return 1;//Mężczyzna
}
else {
return 0;//Kobieta
}
}
else {
return null;
}
}
/////////////////////////////////
protected function checkSum() {
$sum = 1 * $this->pesel[0] +  
3 * $this->pesel[1] +           
7 * $this->pesel[2] +           
9 * $this->pesel[3] +           
1 * $this->pesel[4] +           
3 * $this->pesel[5] +           
7 * $this->pesel[6] +           
9 * $this->pesel[7] +           
1 * $this->pesel[8] +           
3 * $this->pesel[9];            
$sum %= 10;
$sum = 10 - $sum;
$sum %= 10;
 
if ($sum == $this->pesel[10]) {
return 1;
}
else {
return 0;
}
}
////////////////////////////////////
protected function checkMonth() {
$month = $this->getBirthMonth();
if ($month > 0 && $month < 13) {
return 1;
}
else {
return 0;
}
}
//////////////////////////////////
protected function leapYear($year) {
if ($year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0){
  return 1;  
}
else{
  return 0;  
}
}
////////////////////////////////////
protected function checkDay() {
$year = $this->getBirthYear();
$month = $this->getBirthMonth();
$day = $this->getBirthDay();
if (($day >0 && $day < 32) &&
($month == 1 || $month == 3 || $month == 5 ||
$month == 7 || $month == 8 || $month == 10 ||
$month == 12)) {
return 1;
}
else if (($day >0 && $day < 31) &&
($month == 4 || $month == 6 || $month == 9 ||
$month == 11)) {
return 1;
}
else if (($day >0 && $day < 30 && ($this->leapYear($year))) ||
($day >0 && $day < 29 && !$this->leapYear($year))) {
return 1;
}
else {
return 0;
}
}
/////////////////////////////////////

}



