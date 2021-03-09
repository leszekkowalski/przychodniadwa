<?php
declare(strict_types=1);

namespace Kalendarz\Entity;

use Application\Model\Lekarz;

setlocale(LC_TIME, 'pl_PL.UTF-8', 'pl.UTF-8');
define('DNITYGODNIA', array('Nd', 'Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So'));


class Kalendarz
{
    
    public $GODZINYPRACY= array('10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00');
    
    public $KOLORY=array('red','green','blue','orange','red','green','blue','red','green','blue','orange');
    /**
     *
     * @var string - data uzywana w Kalenadzu w formacie RRRR-MM-DD GG:MM:SS 
     */
    public $dataKalendarza;
    /**
     *
     * @var int - miesiac dla którego powstaje kalendarz 
     */
    public $miesiac;
    
  private $poprzedniMiesiac;
  private $iloscDniPoprzedniMiesiac;
  private $nastepnyMiesiac;
  private $iloscDniNastepnyMiesiac;
  
  private $nastepnyRok;
  private $poprzedniRok;
    
    
    /**
     *
     * @var int - rok dla którego powstaje kalendarz 
     */
    public $rok;
    /**
     *
     * @var int - ile dni w miesiącu kalendarza; 
     */
    public $dniMiesiaca;
    /**
     *
     * @var int (0-6) indeks dnia tygodnia w którym  zaczyna się Miesiac;
     */
    
    private $poczatekMiesiaca;
    
    
    private $dzisjaj;


    public function __construct($dataKalendarza=null)
    {
        if(isset($dataKalendarza))
        {
            $this->dataKalendarza=$dataKalendarza;
        }else
        {
            $this->dataKalendarza= date('Y-m-d');
        }
     //konwersja na znacznik czasu oraz wyznaczenie roku i miesjaca dla kalendarza   
        $znacznikCzasu= strtotime($this->dataKalendarza);
        $this->rok= (int) date('Y',$znacznikCzasu);
        $this->miesiac=(int) date('m',$znacznikCzasu);
        $this->dzisjaj=(int) date('d',$znacznikCzasu);
        
        $this->dniMiesiaca= cal_days_in_month(CAL_GREGORIAN, $this->miesiac, $this->rok);
        
        //okreslam dzien tygodnia w jakim zaczyna sie miesiac;
        $znacznikCzasuUnix= mktime(0,0,0,$this->miesiac,1,$this->rok);
        
       $this->poczatekMiesiaca=(int) date('w',$znacznikCzasuUnix);
        
        $this->wyliczPoprzedniINastepnyMiesiac();
    }
    
    private function wyliczPoprzedniINastepnyMiesiac()
    {
    $dataNastepna=strftime('%F',mktime(0, 0, 0, $this->miesiac+1,  1,   $this->rok)); 
    $this->nastepnyMiesiac= (int) substr($dataNastepna, 5, 2);
    $this->nastepnyRok= (int)substr($dataNastepna, 0, 4);
    $this->iloscDniNastepnyMiesiac=cal_days_in_month(CAL_GREGORIAN, $this->nastepnyMiesiac, $this->nastepnyRok);
    
    $dataPoprzedna=strftime('%F',mktime(0, 0, 0, $this->miesiac-1,  1,   $this->rok)); 
    $this->poprzedniMiesiac=(int) substr($dataPoprzedna, 5, 2);
    $this->poprzedniRok= (int) substr($dataPoprzedna, 0, 4);
    $this->iloscDniPoprzedniMiesiac=cal_days_in_month(CAL_GREGORIAN, $this->poprzedniMiesiac, $this->poprzedniRok);
        
    }
    
    public function generujKodHTMLKalendarza(Lekarz $lekarz, $baseUrl,$wydarzeniaArray)
    {
     // $naglowekKalendarza = strftime('%B %Y', strtotime($this->dataKalendarza));  
      
      $wskaznikPoprzedniMiesiac=$this->iloscDniPoprzedniMiesiac-$this->poczatekMiesiaca;
      
      $miesiacUrl=sprintf('%02d',$this->miesiac);
      $html=null;
       //$html="<div id=\"kalendarz\">";
       
      // $html.="<h2>$naglowekKalendarza</h2>"; 
       
       for($i=0,$naglowekKalendarza=null;$i<7;$i++)
       {
          $naglowekKalendarza.= "<li>".DNITYGODNIA[$i]."</li>";
       }
       
       $html.="<ul class=\"dniTygodnia\">".$naglowekKalendarza."</ul>";
       
       $html.="<ul>";//pocztaek nowej listy nieuporzadkowanej 
       
       for($i=1, $c=1, 
       $dzisjajZnacznikDzien=date('j'), $dzisjajZnacznikMiesiac=date('m'),$dzisjajZnacznikRok=date('Y');
       $c<=$this->dniMiesiaca;
       $i++)
       {
           $linki=null;

           //dodaje klase "fill" do pól przed pierwszym dniem miesiaca
           if($i<=$this->poczatekMiesiaca)
           {
             $class='fill';
           }else{
               $class=null;
           }
           
           //dodaje klasę "today" jesli biezaca klasa odpowiada dzisjejszej
           
           if($c==$dzisjajZnacznikDzien && $dzisjajZnacznikMiesiac==$this->miesiac && $dzisjajZnacznikRok==$this->rok && $i!=$c)
           {
               $class='today';
           }
          
          //dodajemy dzień miesiaca w polu kalendarza
          if($this->poczatekMiesiaca<$i && $this->dniMiesiaca>=$c)
          {
            $daneDniDoWpisu_kawalek= sprintf("<strong id='cyfra_powieksz' >%02d</strong>",$c++); 
        
           
           if(isset($wydarzeniaArray[$c-1]))
           {
               foreach ($wydarzeniaArray[$c-1] as $jedno)
               {
               $nazwa=$jedno->getWydarzenie_tytul();
               $idWydarzenie=$jedno->getIdwydarzenie();
               $calyUrl=$baseUrl.'/kalendarz/pokaz-wydarzenie/'.$idWydarzenie.'/'.$lekarz->getIdlekarz();
               $linki.= "<a href='{$calyUrl}' class='link'>{$nazwa}</a>"; 
               }
              
           }
           
         
       $dzienUrl= sprintf('%02d',$c-1);
       $dzisjajUrl=$this->rok.'-'.$miesiacUrl.'-'.$dzienUrl;
       $calyUrl=$baseUrl.'/kalendarz/'.$dzisjajUrl.'/'.$lekarz->getIdlekarz();
       
        $daneDniDoWpisu="<a href={$calyUrl} title='Wpisz wydarzenie' class='cyfra'>{$daneDniDoWpisu_kawalek}</a>";    
            
          }else{
              $daneDniDoWpisu=sprintf("<strong>%02d</strong>",++$wskaznikPoprzedniMiesiac) ; 
          }
          
           $znacznik_Li_start= sprintf("<li class=\"%s\">",$class);
          $znacznik_li_koniec="</li>";
          
          
          //jesli biezacy dzien to sobota zaczynamy nowy wiersz
         if($i!=0 && $i%7==0)
         {
          $nowaLinia="</ul><ul>" ;  
         }else{
           $nowaLinia=null;
         }
         
         //generuj gotowy element listy
         $html.=$znacznik_Li_start.$daneDniDoWpisu.$linki.$znacznik_li_koniec.$nowaLinia;

       }
       
         //wypełniam ostatni tydzien do konca - nie bedacy aktualnym miesiacem
       $dodatkowyKod_NastepnyMiesiac=null;
       $nowyMiesjac=1;
       
       while($i%7!=1)
         {
             $dodatkowyKod_NastepnyMiesiac.=sprintf("<li class=\"fill\"><strong>%02d</strong></li>",$nowyMiesjac++); 
             ++$i;
         }
         
         
       $html=$html.$dodatkowyKod_NastepnyMiesiac;
       
       
       
       $znacznikZamykajacyUl="</ul>"; 
       
       $html.=$znacznikZamykajacyUl;
       return $html;
    }
}

